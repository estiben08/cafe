<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/auth_helper.php';

$body  = json_decode(file_get_contents('php://input'), true);
$token = $body['token'] ?? '';

if (!$token) {
    echo json_encode(['success' => false, 'error' => 'Sin token']);
    exit;
}

try {
    $user = verifyFirebaseIdToken($token);
    if (!$user || empty($user['uid'])) {
        echo json_encode(['success' => false, 'error' => 'Token no válido']);
        exit;
    }

    $firebase_uid = $user['uid'];
    $email        = $user['email'] ?? '';
    $nombre       = $user['nombre'] ?? '';
    $foto         = $user['foto'] ?? '';

    $pdo = getCafePdo();

    // Insertar o actualizar si ya existe
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (firebase_uid, email, nombre, foto)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            email  = IF(VALUES(email) != '', VALUES(email), email),
            nombre = IF(VALUES(nombre) != '', VALUES(nombre), nombre),
            foto   = IF(VALUES(foto) != '', VALUES(foto), foto)
    ");
    $stmt->execute([$firebase_uid, $email, $nombre, $foto]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}