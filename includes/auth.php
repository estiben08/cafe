<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../vendor/autoload.php';

use Firebase\JWT\JWT;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$key = $_ENV['JWT_SECRET'];

// ✅ RATE LIMITING
session_start();
$ip = $_SERVER['REMOTE_ADDR'];
$key_intentos = "intentos_" . $ip;

if (!isset($_SESSION[$key_intentos])) {
    $_SESSION[$key_intentos] = ['cantidad' => 0, 'ultimo' => time()];
}

// Resetear después de 15 minutos
if (time() - $_SESSION[$key_intentos]['ultimo'] > 900) {
    $_SESSION[$key_intentos] = ['cantidad' => 0, 'ultimo' => time()];
}

// Bloquear después de 5 intentos
if ($_SESSION[$key_intentos]['cantidad'] >= 5) {
    $espera = 900 - (time() - $_SESSION[$key_intentos]['ultimo']);
    http_response_code(429);
    echo json_encode([
        "success" => false,
        "error"   => "Demasiados intentos. Espera " . ceil($espera / 60) . " minutos."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['usuario']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
    exit;
}

$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "Error de conexión"]);
    exit;
}

$stmt = $conn->prepare("SELECT password FROM admin WHERE usuario = ? LIMIT 1");
$stmt->bind_param("s", $data['usuario']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row || !password_verify($data['password'], $row['password'])) {
    // ✅ Sumar intento fallido
    $_SESSION[$key_intentos]['cantidad']++;
    $_SESSION[$key_intentos]['ultimo'] = time();

    $restantes = 5 - $_SESSION[$key_intentos]['cantidad'];
    echo json_encode([
        "success" => false,
        "error"   => "Credenciales incorrectas. Intentos restantes: $restantes"
    ]);
    exit;
}

// ✅ Login exitoso — resetear intentos
$_SESSION[$key_intentos] = ['cantidad' => 0, 'ultimo' => time()];

$payload = [
    "sub" => $data['usuario'],
    "iat" => time(),
    "exp" => time() + (60 * 60)
];

$token = JWT::encode($payload, $key, 'HS256');

echo json_encode(["success" => true, "token" => $token]);