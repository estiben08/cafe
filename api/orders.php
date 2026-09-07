<?php
/**
 * orders.php — Gestión de pedidos
 *
 * POST  (público)  → guarda un nuevo pedido desde la tienda
 * GET   (admin)    → lista todos los pedidos con sus ítems
 * PATCH (admin)    → actualiza el estado de un pedido
 */

/* ── CABECERAS ── */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Methods: POST, GET, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

/* ── CONEXIÓN BD ── */
define('DB_HOST',    '127.0.0.1');
define('DB_NAME',    'coffeecol');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET),
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('[CoffeeCol] Error de conexión BD: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo conectar a la base de datos.']);
    exit;
}

/* ── Helper: verificar JWT admin ── */
function verificarAdmin(): void {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION']
        ?? getallheaders()['Authorization']
        ?? '';

    if (!str_starts_with($authHeader, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Token requerido.']);
        exit;
    }

    $token = substr($authHeader, 7);

    // Intentar verificar con Firebase JWT si está disponible
    $vendorJWT = __DIR__ . '/../vendor/autoload.php';
    if (file_exists($vendorJWT)) {
        require_once $vendorJWT;

        // Cargar .env para obtener JWT_SECRET
        $envFile = __DIR__ . '/..';
        if (class_exists('Dotenv\\Dotenv') && file_exists($envFile . '/.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable($envFile);
            $dotenv->safeLoad();
        }

        try {
            $secret = $_ENV['JWT_SECRET'] ?? getenv('JWT_SECRET') ?? '';
            if ($secret) {
                \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($secret, 'HS256'));
                return; // válido
            }
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Token inválido.']);
            exit;
        }
    }

    // Fallback: validación manual (sin verificar firma, solo estructura/expiración)
    try {
        $parts = explode('.', $token);
        if (count($parts) !== 3) throw new RuntimeException('Token malformado');
        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        if (!$payload || empty($payload['sub'])) throw new RuntimeException('Payload inválido');
        if (!empty($payload['exp']) && $payload['exp'] < time()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Token expirado.']);
            exit;
        }
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Token inválido.']);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

/* ════════════════════════════════════════════════
   GET — listar pedidos (admin)
════════════════════════════════════════════════ */
if ($method === 'GET') {
    verificarAdmin();

    $pedidos = $pdo->query('
        SELECT id, nombre, apellido, email, telefono, ciudad, direccion,
               total, estado, fecha
        FROM pedidos
        ORDER BY fecha DESC
    ')->fetchAll();

    if (!$pedidos) {
        echo json_encode(['success' => true, 'pedidos' => []]);
        exit;
    }

    $ids        = implode(',', array_column($pedidos, 'id'));
    $itemsQuery = $pdo->query("
        SELECT pi.pedido_id, pi.producto_id, pi.cantidad, pi.precio_unitario,
               p.nombre
        FROM pedido_items pi
        LEFT JOIN productos p ON p.id = pi.producto_id
        WHERE pi.pedido_id IN ($ids)
    ")->fetchAll();

    $itemsPorPedido = [];
    foreach ($itemsQuery as $item) {
        $itemsPorPedido[$item['pedido_id']][] = $item;
    }

    foreach ($pedidos as &$pedido) {
        $pedido['items'] = $itemsPorPedido[$pedido['id']] ?? [];
        $pedido['total'] = (float) $pedido['total'];
        $pedido['id']    = (int)   $pedido['id'];
    }
    unset($pedido);

    echo json_encode(['success' => true, 'pedidos' => $pedidos]);
    exit;
}

/* ════════════════════════════════════════════════
   PATCH — cambiar estado (admin)
════════════════════════════════════════════════ */
if ($method === 'PATCH') {
    verificarAdmin();

    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['id']) || empty($data['estado'])) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Faltan campos id y estado.']);
        exit;
    }

    $estadosValidos = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado'];
    if (!in_array($data['estado'], $estadosValidos, true)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Estado no válido.']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE pedidos SET estado = :estado WHERE id = :id');
    $stmt->execute([':estado' => $data['estado'], ':id' => (int) $data['id']]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Pedido no encontrado.']);
        exit;
    }

    echo json_encode(['success' => true, 'mensaje' => 'Estado actualizado.']);
    exit;
}

/* ════════════════════════════════════════════════
   POST — guardar pedido (tienda, público)
════════════════════════════════════════════════ */
if ($method === 'POST') {

    $raw = file_get_contents('php://input');
    if (empty($raw)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Cuerpo de la petición vacío.']);
        exit;
    }

    $data = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON inválido: ' . json_last_error_msg()]);
        exit;
    }

    $camposRequeridos = ['nombre', 'apellido', 'email', 'telefono', 'ciudad', 'direccion', 'total', 'items'];
    foreach ($camposRequeridos as $campo) {
        if (empty($data[$campo])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => "Campo requerido faltante: {$campo}"]);
            exit;
        }
    }

    if (!is_array($data['items']) || count($data['items']) === 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'El pedido no contiene productos.']);
        exit;
    }

    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'El email no es válido.']);
        exit;
    }

    $nombre    = trim(strip_tags($data['nombre']));
    $apellido  = trim(strip_tags($data['apellido']));
    $email     = trim($data['email']);
    $telefono  = trim(strip_tags($data['telefono']));
    $ciudad    = trim(strip_tags($data['ciudad']));
    $direccion = trim(strip_tags($data['direccion']));
    $total     = (float) $data['total'];
    $items     = $data['items'];

    try {
        $pdo->beginTransaction();

        $stmtPedido = $pdo->prepare('
            INSERT INTO `pedidos`
                (`nombre`, `apellido`, `email`, `telefono`, `ciudad`, `direccion`, `total`, `estado`)
            VALUES
                (:nombre, :apellido, :email, :telefono, :ciudad, :direccion, :total, "pendiente")
        ');
        $stmtPedido->execute([
            ':nombre'    => $nombre,
            ':apellido'  => $apellido,
            ':email'     => $email,
            ':telefono'  => $telefono,
            ':ciudad'    => $ciudad,
            ':direccion' => $direccion,
            ':total'     => $total,
        ]);

        $pedidoId = (int) $pdo->lastInsertId();
        if (!$pedidoId) throw new RuntimeException('No se obtuvo un ID de pedido tras el INSERT.');

        $stmtItem = $pdo->prepare('
            INSERT INTO `pedido_items`
                (`pedido_id`, `producto_id`, `cantidad`, `precio_unitario`)
            VALUES
                (:pedido_id, :producto_id, :cantidad, :precio_unitario)
        ');

        foreach ($items as $idx => $item) {
            if (
                !isset($item['producto_id'], $item['cantidad'], $item['precio_unitario']) ||
                !is_numeric($item['producto_id']) ||
                !is_numeric($item['cantidad'])    ||
                !is_numeric($item['precio_unitario'])
            ) {
                throw new RuntimeException("Ítem #{$idx} tiene datos inválidos.");
            }
            $stmtItem->execute([
                ':pedido_id'       => $pedidoId,
                ':producto_id'     => (int)   $item['producto_id'],
                ':cantidad'        => (int)   $item['cantidad'],
                ':precio_unitario' => (float) $item['precio_unitario'],
            ]);
        }

        $pdo->commit();
        error_log("[CoffeeCol] ✅ Pedido #{$pedidoId} guardado. Cliente: {$email}. Total: {$total}");

        http_response_code(201);
        echo json_encode([
            'success'   => true,
            'pedido_id' => $pedidoId,
            'mensaje'   => '¡Pedido registrado correctamente!'
        ]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('[CoffeeCol] Error SQL: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error'   => 'Error al guardar el pedido.',
            'debug'   => $e->getMessage()   // ← TEMPORAL: quitar en producción
        ]);
    } catch (RuntimeException $e) {
        $pdo->rollBack();
        error_log('[CoffeeCol] Error lógico: ' . $e->getMessage());
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

/* ── Método no permitido ── */
http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Método no permitido.']);