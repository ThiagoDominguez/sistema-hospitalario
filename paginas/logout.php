<?php
/* `session_start (); `es una función PHP que inicializa una nueva sesión o reanuda la sesión existente.
Por lo general, se usa al comienzo de un script para comenzar o reanudar una sesión, lo que le permite que se almacene y recupere las variables de sesión en varias páginas. */
session_start();
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

header("Location: login.php"); // Redirige al login
exit();
?>