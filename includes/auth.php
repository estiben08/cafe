<?php


require_once __DIR__ . '/../assets/conexion/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Solo POST.']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true) ?? [];
$usuario  = trim($data['usuario'] ?? '');
$password = $data['password'] ?? '';

if (!$usuario || !$password) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Usuario y contraseña requeridos.']);
    exit;
}

$conn = getConnection();
$stmt = $conn->prepare('SELECT password_hash FROM admin_usuarios WHERE usuario = ? LIMIT 1');
$stmt->bind_param('s', $usuario);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$conn->close();

if (!$row || !password_verify($password, $row['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Credenciales incorrectas.']);
    exit;
}

/* ── Generar token diario ── */
$secret  = 'coffeecol_secret_2024';   // Debe coincidir con products.php
$today   = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$token   = hash('sha256', $usuario . '|' . $today . $secret);

echo json_encode([
    'success' => true,
    'token'   => $token,
    'expira'  => $tomorrow,
    'usuario' => $usuario,
]);