<?php
/**
 * perfil.php — API de actualización de perfil de usuario
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../includes/auth_helper.php';

$user = getAuthenticatedUser();
if (!$user || empty($user['uid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado o sesión expirada']);
    exit;
}

$pdo = getCafePdo();

$stmtU = $pdo->prepare("SELECT * FROM usuarios WHERE firebase_uid = ? LIMIT 1");
$stmtU->execute([$user['uid']]);
$usuario = $stmtU->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}
$usuarioId = (int)$usuario['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($usuario['firebase_uid']);
    echo json_encode(['success' => true, 'usuario' => $usuario]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $data['action'] ?? 'actualizar_perfil';

    if ($action === 'metodo_pago') {
        $metodo = trim(strip_tags($data['metodo_pago'] ?? 'Contra entrega en efectivo'));
        $stmt = $pdo->prepare("UPDATE usuarios SET metodo_pago_preferido = ? WHERE id = ?");
        $stmt->execute([$metodo, $usuarioId]);
        echo json_encode(['success' => true, 'mensaje' => 'Método de pago preferido actualizado']);
        exit;
    }

    // Actualizar datos de perfil (nombre, teléfono, fecha de nacimiento)
    $nombre    = trim(strip_tags($data['nombre'] ?? $usuario['nombre']));
    $telefono  = trim(strip_tags($data['telefono'] ?? $usuario['telefono'] ?? ''));
    $cumple    = trim($data['fecha_nacimiento'] ?? $usuario['fecha_nacimiento'] ?? '');

    if (empty($nombre)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'El nombre no puede estar vacío']);
        exit;
    }

    if (!empty($cumple) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $cumple)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'Formato de fecha inválido']);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE usuarios
        SET nombre = ?,
            telefono = ?,
            fecha_nacimiento = ?
        WHERE id = ?
    ");
    $stmt->execute([$nombre, $telefono ?: null, $cumple ?: null, $usuarioId]);

    echo json_encode([
        'success' => true,
        'mensaje' => 'Perfil actualizado exitosamente',
        'usuario' => [
            'nombre'           => $nombre,
            'telefono'         => $telefono,
            'fecha_nacimiento' => $cumple
        ]
    ]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Método no permitido']);