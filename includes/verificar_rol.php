<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require 'C:/xamppp/htdocs/cafe/vendor/autoload.php';

header('Content-Type: application/json');

$body  = json_decode(file_get_contents('php://input'), true);
$token = $body['token'] ?? '';

if (!$token) {
    echo json_encode(['redirect' => '/cafe/index.php']);
    exit;
}

try {
    $firebase = (new Kreait\Firebase\Factory)
        ->withServiceAccount('C:/xamppp/htdocs/cafetantico-firebase-adminsdk-fbsvc-a449960bbb.json');

    $auth           = $firebase->createAuth();
    $verified_token = $auth->verifyIdToken($token);
    $claims         = $verified_token->claims();

        setcookie('fb_token', $token, [
            'expires'  => 0,               // ← 0 = cookie de sesión, muere al cerrar
            'path'     => '/cafe',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);

    if ($claims->get('admin') === true) {
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
