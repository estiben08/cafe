<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require 'C:/xampp/htdocs/cafe/vendor/autoload.php';

$body  = json_decode(file_get_contents('php://input'), true);
$token = $body['token'] ?? '';

if (!$token) {
    echo json_encode(['success' => false, 'error' => 'Sin token']);
    exit;
}

try {
    $firebase = (new Kreait\Firebase\Factory)
        ->withServiceAccount('C:/xampp/htdocs/cafetantico-firebase-adminsdk-fbsvc-a449960bbb.json');

    $auth           = $firebase->createAuth();
    $verified_token = $auth->verifyIdToken($token);
    $claims         = $verified_token->claims();

    $firebase_uid = $claims->get('sub');
    $email        = $claims->get('email') ?? '';
    $nombre       = $claims->get('name') ?? '';
    $foto         = $claims->get('picture') ?? '';

    $pdo = new PDO('mysql:host=127.0.0.1;dbname=coffeecol;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Insertar o actualizar si ya existe
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (firebase_uid, email, nombre, foto)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            email  = VALUES(email),
            nombre = VALUES(nombre),
            foto   = VALUES(foto)
    ");
    $stmt->execute([$firebase_uid, $email, $nombre, $foto]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}