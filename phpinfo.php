<?php
session_start();
unset($_SESSION['intro_vista']);
echo "Sesión limpiada. <a href='index.php'>Ir al index</a>";
?>