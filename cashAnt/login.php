<?php
require_once 'conexion.php';
$errorMessage = isset($_GET['error']) ? $_GET['error'] : '';  
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CASH ANT - LOGIN</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #000;
      font-family: sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      color: white;
    }

    .contenido {
      width: 90%;
      max-width: 400px;
      max-height: 830px;
      padding: 30px 20px;
      background-color: #000;
      text-align: center;
    }

    .logo {
      width: 150px;
      height: 150px;
      background-color: #8CC9AD;
      border-radius: 50%;
      margin: 0 auto 30px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .logo img {
      width: 80px;
      height: 80px;
    }

    .botonLogin {
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: transparent;
      border: 2px solid #8CC9AD;
      color: white;
      padding: 12px;
      border-radius: 25px;
      font-size: 14px;
      margin: 20px 0;
      cursor: pointer;
      transition: background 0.3s;
      width: 100%;
      gap: 10px;
    }
    .botonLogin i,
    .botonLogin img {
      width: 20px;
      height: 20px;
    }
    .botonLogin:hover {
      background-color: #8CC9AD;
      color: black;
    }

    form {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    input[type="text"],
    input[type="password"] {
      padding: 12px;
      font-size: 14px;
      border: none;
      border-bottom: 2px solid #8CC9AD;
      background-color: #333;
      color: white;
    }
    input::placeholder {
      color: #ccc;
    }

    .register-link {
      margin-top: 40px;
      font-size: 14px;
    }
    .register-link a {
      color: #8CC9AD;
      text-decoration: none;
    }
    .register-link a:hover {
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .logo {
        width: 120px;
        height: 120px;
        margin-bottom: 20px;
      }
      .logo img {
        width: 60px;
        height: 60px;
      }
      .botonLogin {
        padding: 10px;
        font-size: 12px;
        gap: 8px;
      }
      .botonLogin i,
      .botonLogin img {
        width: 18px;
        height: 18px;
      }
      form {
        gap: 10px;
      }
      input[type="text"],
      input[type="password"] {
        padding: 10px;
        font-size: 12px;
      }
      .register-link {
        font-size: 12px;
        margin-top: 30px;
      }
    }

    .error {
      color: red;
      font-size: 14px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<div class="contenido">
  <div class="logo">
    <img src="imagenes/sinfondo.png" alt="Logo">
  </div>

  <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=947810257602-tg6uflbl1hfntbu5cllf0dhb23dm6ain.apps.googleusercontent.com&redirect_uri=http://localhost/preCash/google_callback.php&response_type=code&scope=email%20profile&access_type=online" class="botonLogin" style="text-decoration:none;">
    <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google Logo" style="width:20px;height:20px;margin-right:10px;">
    Ingresar con Google
  </a>

  <button class="botonLogin" style="margin-bottom: 60px;">
    <i class="fas fa-mobile-alt"></i>
    Ingresar con n° móvil
  </button>

  <form action="validar_login.php" method="POST">
    <input type="text" name="usuario" placeholder="Usuario o correo electrónico" required style="margin-bottom: 20px;">
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit" class="botonLogin">Iniciar sesión</button>
  </form>
  <?php if ($errorMessage != ''): ?>
    <span class="error"><?php echo $errorMessage; ?></span>
  <?php endif; ?>
  <div class="register-link" style="margin-top: 50px;">
    ¿No tienes una cuenta?
    <br>
    <a href="registro.php">Regístrate acá.</a>
  </div>
</div>
</body>
</html>
