<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once __DIR__ . '/auth_helper.php';

header('Content-Type: application/json');

$body  = json_decode(file_get_contents('php://input'), true);
$token = $body['token'] ?? '';

if (!$token) {
    echo json_encode(['redirect' => '/cafe/index.php']);
    exit;
}

try {
    $user = verifyFirebaseIdToken($token);
    if (!$user || empty($user['uid'])) {
        echo json_encode([
            'redirect' => '/cafe/includes/loginu.php?error=' . urlencode('Sesión inválida.'),
            'debug'    => 'Token verification returned null'
        ]);
        exit;
    }

    setcookie('fb_token', $token, [
        'expires'  => 0,               // 0 = cookie de sesión
        'path'     => '/cafe',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    if (!empty($user['admin'])) {
        echo json_encode(['redirect' => '/cafe/admin/login.php']);
    } else {
        echo json_encode(['redirect' => '/cafe/index.php']);
    }

} catch (Exception $e) {
    echo json_encode([
        'redirect' => '/cafe/includes/loginu.php?error=' . urlencode('Sesión inválida.'),
        'debug'    => $e->getMessage()
    ]);
}
