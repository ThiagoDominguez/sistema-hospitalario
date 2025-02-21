<?php
require_once '../config.php';

/*`header('Content-Type: application/json');` La línea en el código PHP está configurando la respuesta HTTP
Encabezado para especificar que el contenido que devuelve el servidor está en formato JSON. Esto informa el
Cliente (como un navegador web u otra aplicación) que el cuerpo de respuesta estará en formato JSON,
permitiendo que el cliente interprete y maneje adecuadamente los datos que se envían desde el
servidor. */
header('Content-Type: application/json');

/* El código `session_start ();` inicializa una nueva sesión o reanuda una sesión existente en PHP.
SesLos siones son una forma de almacenar información en varias páginas o solicitudes del mismo usuario. */
session_start();
$id_usuario = $_SESSION['usuario_id'];

/* La variable `$query` en el código PHP está almacenando una consulta SQL que selecciona columnas específicas (` ID` y
`mensaje`) de una tabla llamada 'Notificaciones'. */
$query = "SELECT id, mensaje FROM notificaciones WHERE id_usuario = ? AND leido = 0 ORDER BY created_at DESC";
/* La línea ` = ->prepare();` en el código PHP está preparando una instrucción SQL para
ejecución. */
$stmt = $pdo->prepare($query);
/* La línea `->execute([]);` en el código PHP está ejecutando la instrucción SQL preparada
con un enlace de parámetros. El valor de `$id_usuario` se pasa como un parámetro a la consulta SQL. Esto
Ayuda a prevenir los ataques de inyección SQL separando la lógica de consulta SQL de los datos de entrada del usuario. El
Ejecutar el método luego ejecuta la declaración preparada con el valor de parámetro proporcionado, en este caso, el
ID de usuario almacenado en la variable `$id_usuario`. */
$stmt->execute([$id_usuario]);
/* The line ` = ->fetchAll(PDO::FETCH_ASSOC);` in the PHP code is fetching all the
rows returned by the SQL query execution as an associative array. */
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* La línea `echo json_encode ();` en el código PHP está codificando la matriz PHP
`` en una cadena JSON y luego emitiendo esa cadena JSON al cuerpo de respuesta. Este
Permite que los datos recuperados de la base de datos (en este caso, notificaciones) se conviertan en un
Formato JSON que puede consumirse fácilmente mediante aplicaciones o scripts del lado del cliente. El `json_encode`
La función convierte una matriz de PHP en una representación JSON, por lo que es adecuada para transferir datos
betcon el servidor y el cliente en un formato estandarizado. */
echo json_encode($notificaciones);
?>