<?php
header("Content-Type: application/json; charset=utf-8");
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Error de conexión"]);
    exit;
}
$conn->set_charset("utf8mb4");

function verificarToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "No autorizado"]);
        exit;
    }
    $token = str_replace("Bearer ", "", $headers['Authorization']);
    try {
        JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => "Token inválido"]);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM productos ORDER BY id DESC");
    $productos = [];
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    echo json_encode(["success" => true, "productos" => $productos], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($method === 'POST') {
    verificarToken();

    $id           = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
    $nombre       = trim($_POST['nombre']       ?? '');
    $descripcion  = trim($_POST['descripcion']  ?? '');
    $precio       = floatval($_POST['precio']   ?? 0);
    $precio_antes = isset($_POST['precio_antes']) && $_POST['precio_antes'] !== '' ? floatval($_POST['precio_antes']) : null;
    $categoria    = trim($_POST['categoria']    ?? '');
    $badge        = isset($_POST['badge'])      && $_POST['badge']      !== '' ? trim($_POST['badge'])      : null;
    $badge_tipo   = isset($_POST['badge_tipo']) && $_POST['badge_tipo'] !== '' ? trim($_POST['badge_tipo']) : null;
    $unidad       = isset($_POST['unidad'])     && $_POST['unidad']     !== '' ? trim($_POST['unidad'])     : null;

    if (!$nombre || !$precio || !$categoria) {
        echo json_encode(["success" => false, "error" => "Nombre, precio y categoría son obligatorios"]);
        exit;
    }

    $imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            echo json_encode(["success" => false, "error" => "Formato no permitido. Usa jpg, png o webp"]);
            exit;
        }
        $nombre_archivo = uniqid('prod_') . '.' . $ext;
        $destino = __DIR__ . '/../assets/imagenes/' . $nombre_archivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $imagen = 'assets/imagenes/' . $nombre_archivo;
        }
    }

    // Si viene con ID, actualizamos el producto existente
    if ($id && $id > 0) {
        if ($imagen) {
            $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, precio_antes=?, categoria=?, badge=?, badge_tipo=?, unidad=?, imagen=? WHERE id=?");
            $stmt->bind_param("ssddsssssi", $nombre, $descripcion, $precio, $precio_antes, $categoria, $badge, $badge_tipo, $unidad, $imagen, $id);
        } else {
            $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, precio_antes=?, categoria=?, badge=?, badge_tipo=?, unidad=? WHERE id=?");
            $stmt->bind_param("ssddssssi", $nombre, $descripcion, $precio, $precio_antes, $categoria, $badge, $badge_tipo, $unidad, $id);
        }

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $id, "mensaje" => "Producto actualizado con éxito"]);
        } else {
            echo json_encode(["success" => false, "error" => "Error al actualizar: " . $conn->error]);
        }
        exit;
    }

    // De lo contrario, insertamos un nuevo producto
    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, precio_antes, categoria, badge, badge_tipo, unidad, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddsssss", $nombre, $descripcion, $precio, $precio_antes, $categoria, $badge, $badge_tipo, $unidad, $imagen);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al guardar: " . $conn->error]);
    }
    exit;
}

if ($method === 'DELETE') {
    verificarToken();

    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id'] ?? 0);

    if (!$id) {
        echo json_encode(["success" => false, "error" => "ID inválido"]);
        exit;
    }

    $result = $conn->query("SELECT imagen FROM productos WHERE id = $id");
    $row = $result->fetch_assoc();
    if ($row && $row['imagen'] && !str_starts_with($row['imagen'], 'http')) {
        $ruta = __DIR__ . '/../' . $row['imagen'];
        if (file_exists($ruta)) unlink($ruta);
    }

    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al eliminar"]);
    }
    exit;
}

echo json_encode(["error" => "Método no permitido"]);