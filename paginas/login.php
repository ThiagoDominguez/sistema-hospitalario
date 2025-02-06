<?php
session_start();
include 'config.php'; // Configuración de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Verificar si el usuario existe
  $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE Email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['Contraseña'])) {
    // El login es exitoso, almacenamos información del usuario en la sesión
    $_SESSION['usuario_id'] = $user['ID_Usuario'];
    $_SESSION['rol_id'] = $user['ID_Rol'];
    $_SESSION['email'] = $user['Email'];

    // Redirigir a diferentes páginas según el rol
    switch ($user['ID_Rol']) {
      case 1: // Paciente
        header("Location: dashboard.php");
        break;
      case 2: // Médico
        header("Location: dashboard.php");
        break;
      case 3: // Administrativo
        header("Location: dashboard.php");
        break;
      default:
        echo "Rol desconocido.";
    }
  } else {
    $error = "Credenciales incorrectas.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/styles.css">
  <title>Login</title>
</head>

<body>
  <div class="container">
    <h2 class="title">Iniciar sesión</h2>
    <form method="POST" action="login.php" class="form">
      <div class="form-group">
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

      </div>


      <button type="submit" class="btn blue">Iniciar sesión</button>
      <p>¿No tienes una cuenta? <a href="../index.php">Registrate aquí</a>.</p>
    </form>

  </div>

  <?php if (isset($error))
    echo "<p class='error'>$error</p>"; ?>
</body>

</html>