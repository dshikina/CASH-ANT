<?php
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CASH ANT | REGISTRO</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      margin: 0;
      padding: 0;
      background-color: #000;
      color: white;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }

    body {
      display: flex;
      justify-content: center;
      min-height: 100vh;
      padding: 40px 20px;
    }

    .registro {
      width: 100%;
      max-width: 600px;
      padding: 40px 30px;
      background-color: #000;
      text-align: center;
      border-radius: 12px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    label {
      color: #ccc;
      font-size: 14px;
      text-align: left;
    }

    input {
      background-color: #2d2d2d;
      border: 1px solid #fff;
      padding: 12px;
      color: #fff;
      font-size: 14px;
      border-radius: 5px;
    }

    button {
      background-color: #a6e7c0;
      color: #000;
      border: none;
      padding: 12px;
      font-size: 16px;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s;
      margin-top: 20px;
    }

    button:hover {
      background-color: #8cd6ac;
    }

    @media (max-width: 768px) {
      .registro {
        padding: 20px;
      }

      .registro h2 {
        font-size: 16px;
      }

      input {
        font-size: 14px;
      }

      button {
        font-size: 15px;
      }
    }

    @media (max-width: 480px) {
      .registro {
        padding: 15px;
      }

      .registro h2 {
        font-size: 14px;
      }

      input {
        font-size: 12px;
      }

      button {
        font-size: 14px;
      }
    }
  </style>
</head>
<body>
  <section class="registro">
    <form action="validar_registro.php" method="POST">
      <label for="nombre">Nombre</label>
      <input type="text" id="nombre" name="nombre" required>

      <label for="apellidos">Apellidos</label>
      <input type="text" id="apellidos" name="apellidos" required>

      <label for="usuario">Usuario</label>
      <input type="text" id="usuario" name="usuario" required>

      <label for="salario">Salario</label>
      <input type="number" id="salario" name="salario" required>

      <label for="gastos">Porcentaje destinado a gastos</label>
      <input type="number" id="gastos" name="gastos" required>

      <label for="correo">Correo Electrónico</label>
      <input type="email" id="correo" name="correo" required>

      <label for="contrasena">Contraseña</label>
      <input type="password" id="contrasena" name="contrasena" required>

      <button type="submit">Registrarse</button>
    </form>
  </section>
</body>
</html>
