<?php
$host = "localhost";
$db = "u346586528_formulario";
$user = "u346586528_coffecol";
$pass = "Coffe280725";
$conn = new mysqli($host, $user, $pass, $db);
// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?> 