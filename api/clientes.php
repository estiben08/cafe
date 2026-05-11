<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// ── Obtener token (Apache a veces no pasa Authorization) ──
$token = '';

if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
    $token = str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']);
} elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $token = str_replace('Bearer ', '', $_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
} elseif (function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    $token   = str_replace('Bearer ', '', $headers['Authorization'] ?? '');
}

if (!$token) {
    echo json_encode(['success' => false, 'error' => 'Sin autorización']);
    exit;
}

// ── Decodificar JWT ──
$parts = explode('.', $token);
if (count($parts) !== 3) {
    echo json_encode(['success' => false, 'error' => 'Token inválido']);
    exit;
}

try {
    $payload = json_decode(base64_decode(str_replace(['-','_'], ['+','/'], $parts[1])), true);
    if (!$payload || empty($payload['sub'])) throw new Exception('Payload inválido');
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Token inválido']);
    exit;
}

// ── Conexión MySQL ──
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=coffeecol;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Error de base de datos']);
    exit;
}

// ── GET: listar clientes ──
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("
        SELECT
            u.id,
            u.firebase_uid,
            u.nombre,
            u.email,
            u.foto,
            u.puntos,
            u.fecha_nacimiento,
            u.fecha_registro,
            COUNT(DISTINCT p.id) AS total_pedidos
        FROM usuarios u
        LEFT JOIN pedidos p ON p.firebase_uid = u.firebase_uid
        GROUP BY u.id
        ORDER BY u.fecha_registro DESC
    ");
    $clientes = $stmt->fetchAll();

    $stmtH = $pdo->prepare("
        SELECT descripcion, puntos, fecha
        FROM puntos_historial
        WHERE usuario_id = ?
        ORDER BY fecha DESC
        LIMIT 5
    ");

    $stmtAcceso = $pdo->prepare("
        SELECT MAX(fecha) as ultimo_acceso
        FROM pedidos
        WHERE firebase_uid = ?
    ");

    foreach ($clientes as &$c) {
        $stmtH->execute([$c['id']]);
        $c['historial'] = $stmtH->fetchAll();
        $stmtAcceso->execute([$c['firebase_uid']]);
        $row = $stmtAcceso->fetch();
        $c['ultimo_acceso'] = $row['ultimo_acceso'] ?? $c['fecha_registro'];
    }

    echo json_encode(['success' => true, 'clientes' => $clientes]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Método no soportado']);