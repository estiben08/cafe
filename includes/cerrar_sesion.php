<?php
/**
 * cerrar_sesion.php — includes/cerrar_sesion.php
 * Borra la cookie fb_token y destruye la sesión PHP
 */

// Borrar cookie fb_token
setcookie('fb_token', '', [
    'expires'  => time() - 3600,
    'path'     => '/cafe',
    'httponly' => true,
    'samesite' => 'Strict'
]);

// Destruir sesión PHP si existe
if (session_status() === PHP_SESSION_NONE) session_start();
session_destroy();

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
exit;