<?php
/**
 * direcciones.php — API REST para direcciones de usuario
 * GET    → Lista las direcciones del usuario autenticado
 * POST   → Agrega una nueva dirección
 * DELETE → Elimina una dirección por ID
 */

error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
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

// Obtener ID del usuario
$stmtU = $pdo->prepare("SELECT id FROM usuarios WHERE firebase_uid = ? LIMIT 1");
$stmtU->execute([$user['uid']]);
$usuario = $stmtU->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}
$usuarioId = (int)$usuario['id'];

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: Listar direcciones ──
if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT * FROM usuario_direcciones WHERE usuario_id = ? ORDER BY principal DESC, id DESC");
    $stmt->execute([$usuarioId]);
    $direcciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'direcciones' => $direcciones]);
    exit;
}

// ── POST: Crear nueva dirección o eliminar ──
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $data['action'] ?? 'crear';

    if ($action === 'eliminar') {
        $id = (int)($data['id'] ?? 0);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID de dirección requerido']);
            exit;
        }
        $stmtDel = $pdo->prepare("DELETE FROM usuario_direcciones WHERE id = ? AND usuario_id = ?");
        $stmtDel->execute([$id, $usuarioId]);
        echo json_encode(['success' => true, 'mensaje' => 'Dirección eliminada']);
        exit;
    }

    // Crear dirección
    $tipo         = trim(strip_tags($data['tipo'] ?? 'Casa'));
    $direccion    = trim(strip_tags($data['direccion'] ?? ''));
    $ciudad       = trim(strip_tags($data['ciudad'] ?? ''));
    $departamento = trim(strip_tags($data['departamento'] ?? 'Huila'));
    $principal    = !empty($data['principal']) ? 1 : 0;

    if (empty($direccion) || empty($ciudad)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'error' => 'La dirección y ciudad son obligatorias']);
        exit;
    }

    if (!in_array($tipo, ['Casa', 'Trabajo', 'Otra'], true)) {
        $tipo = 'Casa';
    }

    // Si es principal, desmarcar las otras
    if ($principal === 1) {
        $pdo->prepare("UPDATE usuario_direcciones SET principal = 0 WHERE usuario_id = ?")->execute([$usuarioId]);
    } else {
        // Si es la primera dirección del usuario, hacerla principal automáticamente
        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM usuario_direcciones WHERE usuario_id = ?");
        $stmtCount->execute([$usuarioId]);
        if ((int)$stmtCount->fetchColumn() === 0) {
            $principal = 1;
        }
    }

    $stmtIns = $pdo->prepare("
        INSERT INTO usuario_direcciones (usuario_id, tipo, direccion, ciudad, departamento, principal)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmtIns->execute([$usuarioId, $tipo, $direccion, $ciudad, $departamento, $principal]);
    $newId = (int)$pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Dirección guardada correctamente',
        'direccion' => [
            'id'           => $newId,
            'tipo'         => $tipo,
            'direccion'    => $direccion,
            'ciudad'       => $ciudad,
            'departamento' => $departamento,
            'principal'    => $principal
        ]
    ]);
    exit;
}

// ── DELETE: Eliminar dirección ──
if ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    $id = (int)($data['id'] ?? ($_GET['id'] ?? 0));
    if (!$id) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'ID requerido']);
        exit;
    }
    $stmtDel = $pdo->prepare("DELETE FROM usuario_direcciones WHERE id = ? AND usuario_id = ?");
    $stmtDel->execute([$id, $usuarioId]);
    echo json_encode(['success' => true, 'mensaje' => 'Dirección eliminada']);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Método no permitido']);