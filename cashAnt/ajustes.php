<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit();
}

$usuario_id = $_SESSION['usuario_id'];
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "No se encontró el usuario.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CASH ANT | AJUSTES</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="cssMenu.css" />
  <style>
    
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
  font-family: sans-serif;
  background-color: #000;
}

body {
  display: flex;
  flex-direction: column;
  padding-bottom: 170px; 

}

.usuarioContainer {
  display: flex;
  align-items: center;
  gap: 120px;
  margin: 20px auto;
  flex-wrap: wrap;
}

.circuloUsuario {
  width: 140px;
  height: 140px;
  background-color: #8CC9AD;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: black;
  font-size: 40px;
  font-weight: bold;
}

.textoUsuario {
  color: white;
  font-size: 50px;
  text-align: center;
}

.registro {
  width: 100%;
  max-width: 500px;
  margin: 20px auto 200px auto;
  padding: 30px;
  padding-bottom: 200px;
  border-radius: 20px;
}

.registro form {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.registro label {
  color: #ccc;
  font-size: 14px;
}

.registro input {
  background-color: #1f1f1f;
  border: 1px solid #444;
  padding: 12px;
  color: #fff;
  font-size: 14px;
  border-radius: 8px;
  transition: border 0.3s;
}

.registro input:focus {
  border-color: #8CC9AD;
  outline: none;
}

.contenedor {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

#save {
  background-color: #8CC9AD;
  border: none;
  color: black;
  padding: 6px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 17px;
}

#save:hover {
  background-color: #76b89d;
}

@media (max-width: 768px) {
  .usuarioContainer {
    gap: 40px;
    text-align: center;
    flex-direction: column;
  }

  .textoUsuario {
    font-size: 30px;
  }

  .registro {
    padding: 20px;
    margin-bottom: 80px;
    padding-bottom: 230px;
    width: 90%;
  }

  .contenedor {
    flex-direction: column;
    align-items: flex-start;
  }

  #save {
    width: 100%;
    padding: 12px 0;
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .usuarioContainer {
    gap: 20px;
  }

  .textoUsuario {
    font-size: 24px;
  }

  .registro input {
    padding: 10px;
    font-size: 16px;
  }
}
@media (min-height: 1001px) and (max-height: 1299px) {
      .menuItem i {
        font-size: 55px;
      }

      .circuloContent img {
        width: 115px;
        height: 115px;
      }

      .homecirculo {
        width: 150px;
        height: 150px;
        bottom: 70px;
      }

      .nuevoGasto {
        margin-top: 100px;
      }

      .nuevoGasto button {
        font-size: 40px;
      }

      .headerHome h3 {
        text-align: center;
        font-size: 30px;
        margin-top: -30px;
        width: 100%;
      }

      .headerHome h2 {
        font-size: 52px;
        margin-top: 15px;
      }

      .nuevoGasto {
        font-size: 30px;
        margin-top: 93px;
      }
    }

    @media (max-height: 999px) {
      .contenedor {
        flex-direction: row;
      }
    }

    @media (min-height: 1000px) and (max-height: 1299px) {
      .registro input {
        font-size: 22px;
      }

      .contenedor {
        flex-direction: row;
      }
      
      .registro {
        max-width: 630px;
        margin-bottom: 200px;
      }

      .registro label {
        color: #ccc;
        font-size: 22px;
      }

      button {
        font-size: 20px;
      }

      #save {
        font-size: 22px;
        margin-top: 20px;
        margin-bottom: 30px;
      }
    }

    @media (min-height: 1300px) {
      .registro {
        max-width: 750px;
        margin-bottom: 200px;
      }

      .contenedor {
        flex-direction: row;
      }

      .registro input {
        font-size: 25px;
      }

      .registro label {
        color: #ccc;
        font-size: 25px;
      }

      button {
        font-size: 23px;
      }

      #save {
        font-size: 29px;
        margin-top: 18px;
        margin-bottom: 30px;
      }

      .usuarioContainer {
        margin: 70px auto 20px auto; 
      }

    }
  </style>
</head>
<body>
  <div class="usuarioContainer">
    <div class="circuloUsuario"><?= strtoupper(substr($usuario['nombre'], 0, 1)) ?></div>
    <div class="textoUsuario"><?= htmlspecialchars($usuario['user']) ?></div>
  </div>

  <section class="registro">
    <form method="POST" action="actualizar_usuario.php">
      <div class="contenedor">
        <label for="nombre">Nombre</label>
        <button type="button">Editar</button>
      </div>
      <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre'] ?? '') ?>" disabled required>

      <div class="contenedor">
        <label for="apellido">Apellido</label>
        <button type="button">Editar</button>
      </div>
      <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($usuario['apellido'] ?? '') ?>" disabled required>

      <div class="contenedor">
        <label for="usuario">Usuario</label>
        <button type="button">Editar</button>
      </div>
      <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario['user']) ?>" disabled required>

      <div class="contenedor">
        <label for="salario_base">Salario</label>
        <button type="button">Editar</button>
      </div>
      <input type="number" id="salario_base" name="salario_base" value="<?= htmlspecialchars($usuario['salario_base'] ?? '') ?>" disabled required>

      <div class="contenedor">
        <label for="porcentaje_gastos">Porcentaje</label>
        <button type="button">Editar</button>
      </div>
      <input type="number" id="porcentaje_gastos" name="porcentaje_gastos" value="<?= htmlspecialchars($usuario['porcentaje_gastos'] ?? '') ?>" disabled required>

      <div class="contenedor">
        <label for="correo">Correo electrónico</label>
        <button type="button">Editar</button>
      </div>
      <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>" disabled required>

      <div class="contenedor">
        <label for="contrasena">Contraseña</label>
        <button type="button">Editar</button>
      </div>
      <input type="password" id="contrasena" name="contrasena" placeholder="******" disabled>

      <button type="submit" id="save">Guardar cambios</button>
    </form>
  </section>

  <div class="menuHome">
    <a href="historial.php" class="menuItem">
      <i class="fas fa-list-ul"></i>
      <p>HISTORIAL</p>
    </a>

    <a href="balance.php" class="menuItem">
      <i class="fas fa-chart-pie"></i>
      <p>BALANCE</p>
    </a>

    <div class="menuSpacer"></div>

    <a href="ajustes.php" class="menuItem">
      <i class="fas fa-cog"></i>
      <p>AJUSTES</p>
    </a>

    <a href="seguimiento.php" class="menuItem">
      <i class="fas fa-user"></i>
      <p>SEGUIMIENTO</p>
    </a>

    <a href="home.php" class="homecirculo">
      <div class="circuloContent">
        <img src="imagenes/sinfondo.png" alt="sinFondo" />
      </div>
    </a>
  </div>

  <script>
    let botonesEditar = document.querySelectorAll(".contenedor button");
    let inputs = document.querySelectorAll("form input");

    botonesEditar.forEach((boton, index) => {
      boton.addEventListener("click", () => {
        inputs[index].disabled = false;
        inputs[index].focus();
        if (inputs[index].id === "correo") {
          inputs[index].disabled = false;
        }
      });
    });

    document.querySelector("form").addEventListener("submit", function() {
      let correoField = document.getElementById("correo");
      if (correoField.disabled) {
        correoField.disabled = false;
      }
    });
  </script>

</body>
</html>
