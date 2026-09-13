<?php
// Incluye este archivo al inicio de CADA página de administrador así:
// require __DIR__ . '/../includes/proteger_admin.php';

require_once __DIR__ . '/auth_helper.php';

$user = getAuthenticatedUser();

if (!$user) {
    header('Location: /cafe/includes/loginu.php');
    exit;
}

if (empty($user['admin'])) {
    // Es cliente normal o no tiene claim de admin
    header('Location: /cafe/index.php');
    exit;
}

// Si llegó aquí, es admin ✅
