<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../includes/auth_helper.php';

$user = getAuthenticatedUser();
if (!$user || empty($user['uid'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'No autenticado o sesión expirada']);
    exit;
}

$firebase_uid = $user['uid'];

try {
    $pdo = getCafePdo();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de base de datos']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE firebase_uid = ?");
$stmt->execute([$firebase_uid]);
$usuario = $stmt->fetch();

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}
$usuario_id = (int)$usuario['id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['todos'])) {
        $rows = $pdo->prepare("SELECT producto_id FROM usuario_favoritos WHERE usuario_id = ?");
        $rows->execute([$usuario_id]);
        $ids = array_column($rows->fetchAll(), 'producto_id');
        echo json_encode(['success' => true, 'ids' => array_map('intval', $ids)]);
        exit;
    }
    $producto_id = (int)($_GET['producto_id'] ?? 0);
    if (!$producto_id) {
        echo json_encode(['success' => false, 'error' => 'producto_id requerido']);
        exit;
    }
    $check = $pdo->prepare("SELECT id FROM usuario_favoritos WHERE usuario_id = ? AND producto_id = ?");
    $check->execute([$usuario_id, $producto_id]);
    echo json_encode(['success' => true, 'favorito' => (bool)$check->fetch()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body        = json_decode(file_get_contents('php://input'), true);
    $producto_id = (int)($body['producto_id'] ?? 0);

    if (!$producto_id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'producto_id requerido']);
        exit;
    }

    $check = $pdo->prepare("SELECT id FROM usuario_favoritos WHERE usuario_id = ? AND producto_id = ?");
    $check->execute([$usuario_id, $producto_id]);
    $existe = $check->fetch();

    if ($existe) {
        $pdo->prepare("DELETE FROM usuario_favoritos WHERE usuario_id = ? AND producto_id = ?")
            ->execute([$usuario_id, $producto_id]);
        echo json_encode(['success' => true, 'accion' => 'eliminado', 'favorito' => false]);
    } else {
        $pdo->prepare("INSERT INTO usuario_favoritos (usuario_id, producto_id) VALUES (?, ?)")
            ->execute([$usuario_id, $producto_id]);
        echo json_encode(['success' => true, 'accion' => 'agregado', 'favorito' => true]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Método no permitido']);