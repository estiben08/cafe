<?php
// Este archivo es llamado por navigator.sendBeacon() cuando el usuario
// cierra la pestaña. Destruye la sesión PHP para que la próxima vez
// que entre al index, vea la intro + modal de nuevo.
session_start();
unset($_SESSION['intro_vista']);
session_write_close();
?>