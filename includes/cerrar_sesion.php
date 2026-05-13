<?php
foreach (['/', '/cafe', '/cafe/includes'] as $path) {
    setcookie('fb_token', '', [
        'expires'  => time() - 3600,
        'path'     => $path,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

if (session_status() === PHP_SESSION_NONE) session_start();
session_destroy();

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
exit;