<?php
include 'conexion.php'; // o ajustar ruta según sea necesario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $empresa = trim($_POST['empresa']);
    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);
    $comentarios = trim($_POST['comentarios']);

    $stmt = $conn->prepare("INSERT INTO contactos (nombre_completo, empresa, telefono, correo_electronico, comentarios) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $empresa, $telefono, $correo, $comentarios);

    if ($stmt->execute()) {
        echo "<script>alert('Mensaje enviado correctamente.'); window.location.href='contacto.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>