<?php
// Incluye este archivo al inicio de CADA página de administrador así:
// require __DIR__ . '/../includes/proteger_admin.php';

require 'C:/xamppp/htdocs/cafe/vendor/autoload.php';

$token = $_COOKIE['fb_token'] ?? '';

if (!$token) {
    header('Location: /cafe/includes/loginu.php');
    exit;
}

try {
    $firebase = (new Kreait\Firebase\Factory)
        ->withServiceAccount('C:/xamppp/htdocs/cafetantico-firebase-adminsdk-fbsvc-a449960bbb.json');

    $auth           = $firebase->createAuth();
    $verified_token = $auth->verifyIdToken($token);
    $claims         = $verified_token->claims();

    if ($claims->get('admin') !== true) {
        // Es cliente normal, no tiene acceso
        header('Location: /cafe/index.php');
        exit;
    }

    // Si llegó aquí, es admin ✅
    // Puedes usar $verified_token->claims()->get('email') para obtener su email

} catch (Exception $e) {
    // Token inválido o expirado
    setcookie('fb_token', '', time() - 3600, '/cafe');
    header('Location: /cafe/includes/loginu.php?error=' . urlencode('Sesión expirada. Inicia sesión de nuevo.'));
    exit;
}
