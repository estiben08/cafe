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
    echo json_encode(["error" => "Error de conexión con la base de datos"]);
    exit;
}
$conn->set_charset("utf8mb4");

function verificarToken() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "No autorizado"]);
        exit;
    }
    $token = str_replace("Bearer ", "", $authHeader);
    try {
        return JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["success" => false, "error" => "Token inválido o expirado"]);
        exit;
    }
}

function esAdminAutenticado() {
    $headers = getallheaders();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (!$authHeader) return false;
    $token = str_replace("Bearer ", "", $authHeader);
    try {
        JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

// ── GET: Obtener anuncios ──
if ($method === 'GET') {
    $esAdmin = esAdminAutenticado();

    // Si es admin o pide lista completa explícitamente con token
    if ($esAdmin && !isset($_GET['public'])) {
        $result = $conn->query("SELECT * FROM anuncios ORDER BY activo DESC, id DESC");
        $anuncios = [];
        while ($row = $result->fetch_assoc()) {
            $anuncios[] = $row;
        }
        echo json_encode(["success" => true, "anuncios" => $anuncios], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Consulta pública: único anuncio activo actual
    $result = $conn->query("SELECT * FROM anuncios WHERE activo = 1 ORDER BY id DESC LIMIT 1");
    $anuncio = $result->fetch_assoc();
    echo json_encode(["success" => true, "anuncio" => $anuncio ?: null], JSON_UNESCAPED_UNICODE);
    exit;
}

// ── POST: Crear, Editar, Toggle o Eliminar ──
if ($method === 'POST') {
    verificarToken();

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // 1. Acción TOGGLE (activar/desactivar rápido)
    if ($action === 'toggle') {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true) ?: $_POST;
        $id = intval($data['id'] ?? 0);
        $activo = isset($data['activo']) ? intval($data['activo']) : null;

        if (!$id) {
            echo json_encode(["success" => false, "error" => "ID de anuncio no proporcionado"]);
            exit;
        }

        if ($activo === null) {
            // Invertir estado actual
            $curr = $conn->query("SELECT activo FROM anuncios WHERE id = $id")->fetch_assoc();
            $activo = ($curr && (int)$curr['activo'] === 1) ? 0 : 1;
        }

        if ($activo === 1) {
            // Desactivar los demás para mantener 1 anuncio popup principal
            $conn->query("UPDATE anuncios SET activo = 0 WHERE id != $id");
        }

        $stmt = $conn->prepare("UPDATE anuncios SET activo = ? WHERE id = ?");
        $stmt->bind_param("ii", $activo, $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $id, "activo" => $activo, "mensaje" => $activo ? "Anuncio activado" : "Anuncio desactivado"]);
        } else {
            echo json_encode(["success" => false, "error" => "Error al actualizar estado: " . $conn->error]);
        }
        exit;
    }

    // 2. Acción DELETE
    if ($action === 'delete') {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true) ?: $_POST;
        $id = intval($data['id'] ?? 0);

        if (!$id) {
            echo json_encode(["success" => false, "error" => "ID inválido"]);
            exit;
        }

        // Obtener imagen para eliminarla del disco si fue subida
        $res = $conn->query("SELECT imagen FROM anuncios WHERE id = $id");
        if ($row = $res->fetch_assoc()) {
            if ($row['imagen'] && str_starts_with($row['imagen'], 'assets/imagenes/anuncio_')) {
                $ruta = __DIR__ . '/../' . $row['imagen'];
                if (file_exists($ruta)) @unlink($ruta);
            }
        }

        $stmt = $conn->prepare("DELETE FROM anuncios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "mensaje" => "Anuncio eliminado"]);
        } else {
            echo json_encode(["success" => false, "error" => "Error al eliminar: " . $conn->error]);
        }
        exit;
    }

    // 3. Crear / Actualizar Anuncio
    $id                 = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
    $titulo             = trim($_POST['titulo'] ?? '');
    $subtitulo          = trim($_POST['subtitulo'] ?? '');
    $eyebrow            = trim($_POST['eyebrow'] ?? 'Transmisión en vivo · Neiva, Huila');
    $badge_texto        = trim($_POST['badge_texto'] ?? 'Café Región · Neiva, Huila');
    $tipo               = trim($_POST['tipo'] ?? 'partido');
    $mostrar_partido    = isset($_POST['mostrar_partido']) ? intval($_POST['mostrar_partido']) : 0;
    $fecha_evento_texto = trim($_POST['fecha_evento_texto'] ?? '');
    $equipo1_nombre     = trim($_POST['equipo1_nombre'] ?? 'Colombia');
    $equipo1_bandera    = trim($_POST['equipo1_bandera'] ?? 'co');
    $equipo2_nombre     = trim($_POST['equipo2_nombre'] ?? 'Costa Rica');
    $equipo2_bandera    = trim($_POST['equipo2_bandera'] ?? 'cr');
    $fecha_objetivo     = trim($_POST['fecha_objetivo'] ?? '');
    $feat1_icono        = trim($_POST['feat1_icono'] ?? 'fa-solid fa-tv');
    $feat1_texto        = trim($_POST['feat1_texto'] ?? 'Pantallas<br>4K');
    $feat2_icono        = trim($_POST['feat2_icono'] ?? 'fa-solid fa-users');
    $feat2_texto        = trim($_POST['feat2_texto'] ?? 'Ambiente<br>futbolero');
    $feat3_icono        = trim($_POST['feat3_icono'] ?? 'fa-brands fa-java');
    $feat3_texto        = trim($_POST['feat3_texto'] ?? 'Bebidas<br>premium');
    $boton_texto        = trim($_POST['boton_texto'] ?? '');
    $boton_enlace       = trim($_POST['boton_enlace'] ?? '');
    $activo             = isset($_POST['activo']) ? intval($_POST['activo']) : 1;

    if (!$titulo) {
        echo json_encode(["success" => false, "error" => "El título del anuncio es obligatorio"]);
        exit;
    }

    // Formatear fecha_objetivo si viene vacía
    if (!$fecha_objetivo) {
        $fecha_objetivo = date('Y-m-d H:i:s', strtotime('+7 days'));
    } else {
        $fecha_objetivo = date('Y-m-d H:i:s', strtotime($fecha_objetivo));
    }

    // Procesar subida de imagen
    $imagen = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            echo json_encode(["success" => false, "error" => "Formato de imagen no permitido. Usa JPG, PNG o WebP"]);
            exit;
        }
        $nombre_archivo = uniqid('anuncio_') . '.' . $ext;
        $destino = __DIR__ . '/../assets/imagenes/' . $nombre_archivo;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $imagen = 'assets/imagenes/' . $nombre_archivo;
        }
    } elseif (!empty($_POST['imagen_existente'])) {
        $imagen = trim($_POST['imagen_existente']);
    }

    // Si el anuncio va a estar activo, desactivamos los otros para que sea el principal
    if ($activo === 1) {
        if ($id) {
            $conn->query("UPDATE anuncios SET activo = 0 WHERE id != $id");
        } else {
            $conn->query("UPDATE anuncios SET activo = 0");
        }
    }

    // Actualizar registro existente
    if ($id && $id > 0) {
        if ($imagen) {
            $stmt = $conn->prepare("UPDATE anuncios SET
                titulo = ?, subtitulo = ?, eyebrow = ?, badge_texto = ?, imagen = ?, tipo = ?,
                mostrar_partido = ?, fecha_evento_texto = ?, equipo1_nombre = ?, equipo1_bandera = ?,
                equipo2_nombre = ?, equipo2_bandera = ?, fecha_objetivo = ?, feat1_icono = ?, feat1_texto = ?,
                feat2_icono = ?, feat2_texto = ?, feat3_icono = ?, feat3_texto = ?, boton_texto = ?,
                boton_enlace = ?, activo = ?
                WHERE id = ?");
            $stmt->bind_param(
                "ssssssissssssssssssssii",
                $titulo, $subtitulo, $eyebrow, $badge_texto, $imagen, $tipo,
                $mostrar_partido, $fecha_evento_texto, $equipo1_nombre, $equipo1_bandera,
                $equipo2_nombre, $equipo2_bandera, $fecha_objetivo, $feat1_icono, $feat1_texto,
                $feat2_icono, $feat2_texto, $feat3_icono, $feat3_texto, $boton_texto,
                $boton_enlace, $activo, $id
            );
        } else {
            $stmt = $conn->prepare("UPDATE anuncios SET
                titulo = ?, subtitulo = ?, eyebrow = ?, badge_texto = ?, tipo = ?,
                mostrar_partido = ?, fecha_evento_texto = ?, equipo1_nombre = ?, equipo1_bandera = ?,
                equipo2_nombre = ?, equipo2_bandera = ?, fecha_objetivo = ?, feat1_icono = ?, feat1_texto = ?,
                feat2_icono = ?, feat2_texto = ?, feat3_icono = ?, feat3_texto = ?, boton_texto = ?,
                boton_enlace = ?, activo = ?
                WHERE id = ?");
            $stmt->bind_param(
                "sssssissssssssssssssii",
                $titulo, $subtitulo, $eyebrow, $badge_texto, $tipo,
                $mostrar_partido, $fecha_evento_texto, $equipo1_nombre, $equipo1_bandera,
                $equipo2_nombre, $equipo2_bandera, $fecha_objetivo, $feat1_icono, $feat1_texto,
                $feat2_icono, $feat2_texto, $feat3_icono, $feat3_texto, $boton_texto,
                $boton_enlace, $activo, $id
            );
        }

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "id" => $id, "mensaje" => "Anuncio actualizado con éxito"]);
        } else {
            echo json_encode(["success" => false, "error" => "Error al actualizar anuncio: " . $conn->error]);
        }
        exit;
    }

    // Insertar nuevo registro
    if (!$imagen) {
        $imagen = 'assets/imagenes/tantoooo.png';
    }

    $stmt = $conn->prepare("INSERT INTO anuncios (
        titulo, subtitulo, eyebrow, badge_texto, imagen, tipo, mostrar_partido,
        fecha_evento_texto, equipo1_nombre, equipo1_bandera, equipo2_nombre, equipo2_bandera,
        fecha_objetivo, feat1_icono, feat1_texto, feat2_icono, feat2_texto, feat3_icono, feat3_texto,
        boton_texto, boton_enlace, activo
    ) VALUES (
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?,
        ?, ?, ?, ?, ?, ?, ?,
        ?, ?, ?
    )");

    $stmt->bind_param(
        "ssssssissssssssssssssi",
        $titulo, $subtitulo, $eyebrow, $badge_texto, $imagen, $tipo, $mostrar_partido,
        $fecha_evento_texto, $equipo1_nombre, $equipo1_bandera, $equipo2_nombre, $equipo2_bandera,
        $fecha_objetivo, $feat1_icono, $feat1_texto, $feat2_icono, $feat2_texto, $feat3_icono, $feat3_texto,
        $boton_texto, $boton_enlace, $activo
    );

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "id" => $conn->insert_id, "mensaje" => "Anuncio creado con éxito"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al guardar anuncio: " . $conn->error]);
    }
    exit;
}

// ── DELETE ──
if ($method === 'DELETE') {
    verificarToken();
    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id'] ?? 0);

    if (!$id) {
        echo json_encode(["success" => false, "error" => "ID inválido"]);
        exit;
    }

    $res = $conn->query("SELECT imagen FROM anuncios WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        if ($row['imagen'] && str_starts_with($row['imagen'], 'assets/imagenes/anuncio_')) {
            $ruta = __DIR__ . '/../' . $row['imagen'];
            if (file_exists($ruta)) @unlink($ruta);
        }
    }

    $stmt = $conn->prepare("DELETE FROM anuncios WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "mensaje" => "Anuncio eliminado correctamente"]);
    } else {
        echo json_encode(["success" => false, "error" => "Error al eliminar: " . $conn->error]);
    }
    exit;
}

http_response_code(405);
echo json_encode(["error" => "Método no permitido"]);
