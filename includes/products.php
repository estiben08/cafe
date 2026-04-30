<?php
/**
 * CoffeeCol — API REST de Productos
 *
 * GET    /api/products.php              → Lista todos los productos activos
 * GET    /api/products.php?id=N         → Un producto por ID
 * GET    /api/products.php?all=1        → Lista incluyendo inactivos (admin)
 * POST   /api/products.php              → Crear producto   (requiere token)
 * PUT    /api/products.php?id=N         → Editar producto  (requiere token)
 * DELETE /api/products.php?id=N         → Borrar producto  (requiere token)
 *
 * Autenticación: Header   Authorization: Bearer <token>
 * El token se genera al hacer login en /api/auth.php
 */

require_once __DIR__ . '/../assets/conexion/config.php';

/* ── Enrutador ───────────────────────────────────────────── */
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {
    case 'GET':    handleGet($id);    break;
    case 'POST':   requireAuth(); handlePost();      break;
    case 'PUT':    requireAuth(); handlePut($id);    break;
    case 'DELETE': requireAuth(); handleDelete($id); break;
    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
}

/* ═══════════════════════════════════════════════════════════
   AUTENTICACIÓN SIMPLE CON SESIÓN PHP
═══════════════════════════════════════════════════════════ */
function requireAuth(): void {
    // Leer token del header Authorization: Bearer <token>
    $headers = getallheaders();
    $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';

    if (!preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Token requerido.']);
        exit;
    }

    $token = $m[1];
    // El token es un hash SHA-256 de: usuario + '|' + fecha (Y-m-d) + secreto
    $secret  = 'coffeecol_secret_2024';          // Cambia esto en producción
    $today   = date('Y-m-d');
    $expected = hash('sha256', 'admin|' . $today . $secret);

    if (!hash_equals($expected, $token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Token inválido o expirado.']);
        exit;
    }
}

/* ═══════════════════════════════════════════════════════════
   GET — Listar / obtener un producto
═══════════════════════════════════════════════════════════ */
function handleGet(?int $id): void {
    $conn     = getConnection();
    $soloActivos = empty($_GET['all']);   // ?all=1 muestra inactivos (panel admin)

    if ($id) {
        // Un producto
        $stmt = $conn->prepare(
            'SELECT * FROM productos WHERE id = ? ' . ($soloActivos ? 'AND activo = 1' : '')
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Producto no encontrado.']);
            return;
        }
        echo json_encode(['success' => true, 'producto' => formatProducto($row)]);

    } else {
        // Todos
        $where = $soloActivos ? 'WHERE activo = 1' : '';
        $res   = $conn->query("SELECT * FROM productos {$where} ORDER BY id ASC");
        $rows  = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = formatProducto($row);
        }
        echo json_encode(['success' => true, 'total' => count($rows), 'productos' => $rows]);
    }

    $conn->close();
}

/* ═══════════════════════════════════════════════════════════
   POST — Crear producto
═══════════════════════════════════════════════════════════ */
function handlePost(): void {
    $data = getBody();
    $err  = validarCampos($data);
    if ($err) { sendError(400, $err); return; }

    $conn = getConnection();
    $stmt = $conn->prepare(
        'INSERT INTO productos
            (nombre, precio, precio_antes, unidad, icono, categoria,
             badge, badge_tipo, descripcion, rating_valor, rating_cantidad, activo)
         VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
    );

    $activo = 1;
    $stmt->bind_param(
        'sddssssssdia',
        $data['nombre'],
        $data['precio'],
        $data['precio_antes'],
        $data['unidad'],
        $data['icono'],
        $data['categoria'],
        $data['badge'],
        $data['badge_tipo'],
        $data['descripcion'],
        $data['rating_valor'],
        $data['rating_cantidad'],
        $activo
    );

    if ($stmt->execute()) {
        $id = $conn->insert_id;
        http_response_code(201);
        echo json_encode(['success' => true, 'id' => $id, 'message' => 'Producto creado correctamente.']);
    } else {
        sendError(500, 'No se pudo crear el producto: ' . $conn->error);
    }

    $conn->close();
}

/* ═══════════════════════════════════════════════════════════
   PUT — Editar producto
═══════════════════════════════════════════════════════════ */
function handlePut(?int $id): void {
    if (!$id) { sendError(400, 'ID requerido.'); return; }

    $data = getBody();
    $err  = validarCampos($data);
    if ($err) { sendError(400, $err); return; }

    $conn = getConnection();
    $stmt = $conn->prepare(
        'UPDATE productos SET
            nombre=?, precio=?, precio_antes=?, unidad=?, icono=?,
            categoria=?, badge=?, badge_tipo=?, descripcion=?,
            rating_valor=?, rating_cantidad=?, activo=?
         WHERE id=?'
    );

    $stmt->bind_param(
        'sddssssssdiiii',
        $data['nombre'],
        $data['precio'],
        $data['precio_antes'],
        $data['unidad'],
        $data['icono'],
        $data['categoria'],
        $data['badge'],
        $data['badge_tipo'],
        $data['descripcion'],
        $data['rating_valor'],
        $data['rating_cantidad'],
        $data['activo'],
        $id
    );

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            sendError(404, 'Producto no encontrado.');
        } else {
            echo json_encode(['success' => true, 'message' => 'Producto actualizado.']);
        }
    } else {
        sendError(500, 'No se pudo actualizar: ' . $conn->error);
    }

    $conn->close();
}

/* ═══════════════════════════════════════════════════════════
   DELETE — Eliminar producto (soft delete: activo = 0)
═══════════════════════════════════════════════════════════ */
function handleDelete(?int $id): void {
    if (!$id) { sendError(400, 'ID requerido.'); return; }

    $conn = getConnection();

    // Hard delete — cambia a UPDATE activo=0 si prefieres soft delete
    $stmt = $conn->prepare('DELETE FROM productos WHERE id = ?');
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            sendError(404, 'Producto no encontrado.');
        } else {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado.']);
        }
    } else {
        sendError(500, 'No se pudo eliminar: ' . $conn->error);
    }

    $conn->close();
}

/* ═══════════════════════════════════════════════════════════
   HELPERS
═══════════════════════════════════════════════════════════ */
function getBody(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function formatProducto(array $row): array {
    return [
        'id'               => (int)$row['id'],
        'nombre'           => $row['nombre'],
        'precio'           => (float)$row['precio'],
        'precio_antes'     => $row['precio_antes'] !== null ? (float)$row['precio_antes'] : null,
        'unidad'           => $row['unidad'],
        'icono'            => $row['icono'],
        'categoria'        => $row['categoria'],
        'badge'            => $row['badge'],
        'badge_tipo'       => $row['badge_tipo'],
        'descripcion'      => $row['descripcion'],
        'rating_valor'     => (float)$row['rating_valor'],
        'rating_cantidad'  => (int)$row['rating_cantidad'],
        'activo'           => (bool)$row['activo'],
    ];
}

function validarCampos(array $d): ?string {
    if (empty($d['nombre']))    return 'El nombre es obligatorio.';
    if (empty($d['precio']))    return 'El precio es obligatorio.';
    if (!is_numeric($d['precio'])) return 'El precio debe ser un número.';
    if (empty($d['unidad']))    return 'La unidad es obligatoria.';
    if (empty($d['categoria'])) return 'La categoría es obligatoria.';

    // Castings seguros para valores opcionales
    $d['precio_antes']    = isset($d['precio_antes']) && $d['precio_antes'] !== '' ? (float)$d['precio_antes'] : null;
    $d['badge']           = $d['badge']     ?? null;
    $d['badge_tipo']      = $d['badge_tipo'] ?? null;
    $d['descripcion']     = $d['descripcion'] ?? '';
    $d['rating_valor']    = isset($d['rating_valor']) ? (float)$d['rating_valor'] : 5.0;
    $d['rating_cantidad'] = isset($d['rating_cantidad']) ? (int)$d['rating_cantidad'] : 0;
    $d['activo']          = isset($d['activo']) ? (int)$d['activo'] : 1;

    return null;
}

function sendError(int $code, string $msg): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
}