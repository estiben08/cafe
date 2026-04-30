<?php
/**
 * CoffeeCol — Configuración de base de datos
 * Edita las constantes antes de subir al servidor.
 */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // ← tu usuario MySQL
define('DB_PASS', '');           // ← tu contraseña MySQL
define('DB_NAME', 'coffeecol_db');

/**
 * Retorna una conexión MySQLi lista para usar.
 * Termina con JSON de error y HTTP 500 si falla.
 */
function getConnection(): mysqli {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
        exit;
    }

    $conn->set_charset('utf8mb4');
    return $conn;
}

/* ── Headers CORS + JSON ── */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}