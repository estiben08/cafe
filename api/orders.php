<?php
/**
 * orders.php — Endpoint para guardar pedidos en la base de datos
 * Ruta sugerida: /api/orders.php
 *
 * Recibe un POST con JSON:
 * {
 *   nombre, apellido, email, telefono, ciudad, direccion, total,
 *   items: [ { producto_id, cantidad, precio_unitario }, ... ]
 * }
 */

/* ── 1. CABECERAS ─────────────────────────────────────────── */
header('Content-Type: application/json; charset=utf-8');

// Permite peticiones desde el mismo origen (ajusta si usas dominio externo)
header('Access-Control-Allow-Origin: ' . ($_SERVER['HTTP_ORIGIN'] ?? '*'));
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Pre-flight CORS (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

/* ── 2. SOLO ACEPTA POST ──────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Usa POST.']);
    exit;
}

/* ── 3. LEER Y DECODIFICAR BODY JSON ─────────────────────── */
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

/* ── 4. VALIDACIÓN DE CAMPOS REQUERIDOS ──────────────────── */
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

/* ── 5. SANITIZACIÓN ─────────────────────────────────────── */
$nombre    = trim(strip_tags($data['nombre']));
$apellido  = trim(strip_tags($data['apellido']));
$email     = trim($data['email']);
$telefono  = trim(strip_tags($data['telefono']));
$ciudad    = trim(strip_tags($data['ciudad']));
$direccion = trim(strip_tags($data['direccion']));
$total     = (float) $data['total'];
$items     = $data['items'];

/* ── 6. CONEXIÓN A LA BASE DE DATOS ──────────────────────── */
// IMPORTANTE: ajusta estas credenciales a las de tu servidor
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'coffeecol');
define('DB_USER', 'root');       // ← cambia según tu entorno
define('DB_PASS', '');           // ← cambia según tu entorno
define('DB_CHARSET', 'utf8mb4');

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    DB_HOST, DB_NAME, DB_CHARSET
);

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('[CoffeeCol] Error de conexión BD: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'No se pudo conectar a la base de datos.']);
    exit;
}

/* ── 7. GUARDAR PEDIDO EN TRANSACCIÓN ────────────────────── */
try {
    $pdo->beginTransaction();

    /* 7a. INSERT en `pedidos` -------------------------------- */
    $sqlPedido = '
        INSERT INTO `pedidos`
            (`nombre`, `apellido`, `email`, `telefono`, `ciudad`, `direccion`, `total`, `estado`)
        VALUES
            (:nombre, :apellido, :email, :telefono, :ciudad, :direccion, :total, "pendiente")
    ';

    $stmtPedido = $pdo->prepare($sqlPedido);
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

    if (!$pedidoId) {
        throw new RuntimeException('No se obtuvo un ID de pedido tras el INSERT.');
    }

    /* 7b. INSERT de cada ítem en `pedido_items` -------------- */
    $sqlItem = '
        INSERT INTO `pedido_items`
            (`pedido_id`, `producto_id`, `cantidad`, `precio_unitario`)
        VALUES
            (:pedido_id, :producto_id, :cantidad, :precio_unitario)
    ';

    $stmtItem = $pdo->prepare($sqlItem);

    foreach ($items as $idx => $item) {

        // Validar estructura de cada ítem
        if (
            !isset($item['producto_id'], $item['cantidad'], $item['precio_unitario']) ||
            !is_numeric($item['producto_id']) ||
            !is_numeric($item['cantidad'])    ||
            !is_numeric($item['precio_unitario'])
        ) {
            throw new RuntimeException("Ítem #{$idx} tiene datos inválidos.");
        }

        $stmtItem->execute([
            ':pedido_id'      => $pedidoId,
            ':producto_id'    => (int)   $item['producto_id'],
            ':cantidad'       => (int)   $item['cantidad'],
            ':precio_unitario'=> (float) $item['precio_unitario'],
        ]);
    }

    $pdo->commit();

    /* 7c. Respuesta de éxito -------------------------------- */
    error_log("[CoffeeCol] ✅ Pedido #{$pedidoId} guardado. Cliente: {$email}. Total: {$total}");

    http_response_code(201);
    echo json_encode([
        'success'   => true,
        'pedido_id' => $pedidoId,
        'mensaje'   => '¡Pedido registrado correctamente!'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('[CoffeeCol] Error SQL al guardar pedido: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al guardar el pedido en la base de datos.']);

} catch (RuntimeException $e) {
    $pdo->rollBack();
    error_log('[CoffeeCol] Error lógico en pedido: ' . $e->getMessage());
    http_response_code(422);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}