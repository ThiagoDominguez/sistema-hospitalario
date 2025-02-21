<?php
// Cargar variables de entorno desde un archivo .env
require_once 'vendor/autoload.php';
use Dotenv\Dotenv;

/* El código `= dotenv :: createMutable (__ dir); -> load (); `está cargando y analizando el
Contenido de un archivo `.env` ubicado en el mismo directorio que el script PHP. El archivo `.env` típicamente contiene variables de entorno en el formato de pares `key = value`. */
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Obtener las credenciales de la base de datos desde las variables de entorno
$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASS'];
$dbname = $_ENV['DB_NAME'];

try {
  /* La línea `=" mysql: host =; dbname =; charset = utf8mb4 ";` está construyendo un datos
  Nombre de origen (DSN) Cadena para conectarse a una base de datos MySQL utilizando PDO (objetos de datos PHP).*/
  $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
  /* La matriz está configurando las opciones para la conexión PDO (objetos de datos PHP) a un
  Base de datos MySQL.*/
  $options = [
    /* `PDO :: attr_errmode => PDO :: errmode_exception` está configurando el modo de manejo de errores para PDO (PHP
    Objetos de datos) a `PDO :: errmode_exception`. Esto significa que cuando ocurre un error durante una
    Operación de la base de datos, PDO lanzará una excepción, lo que le permitirá atrapar y manejar la excepción
    en tu código. Usar `pdo :: errmode_exception` es una práctica recomendada, ya que ayuda en adecuado
    Manejo de errores y facilita la identificación y resolución de problemas de problemas relacionados con la base de datos. */
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    /* `PDO :: Attr_Default_Fetch_Mode => PDO :: Fetch_assoc` está configurando el modo de búsqueda predeterminado para PDO
    (Objetos de datos PHP) a `PDO :: Fetch_assoc`. Esto significa que al obtener resultados de una base de datos
    consulta usando PDO, los resultados se devolverán como una matriz asociativa donde están los nombres de la columna
    utilizado como claves. Este modo se usa comúnmente para obtener datos de una base de datos, ya que proporciona un
    forma conveniente de acceder a los datos utilizando los nombres de la columna.*/
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    /* `PDO :: Attr_emulate_Prepares => False` está configurando el atributo para el PDO (objetos de datos PHP)
    conexión para deshabilitar declaraciones preparadas emuladas. */
    PDO::ATTR_EMULATE_PREPARES => false,
  ];

  /*La línea `= nueva PDO (,,,);` está creando un nuevo PDO (datos de PHP
  Objetos) Instancia para establecer una conexión a una base de datos MySQL utilizando el DSN proporcionado (datos
  Nombre de la fuente), nombre de usuario, contraseña y opciones de conexión. */
  $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}
/* El `catch (pdoException) {die (" Error de Conexión: ". -> getMessage ()); } `El bloque es una parte de manejo de errores en PHP.*/
?>