
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CASH ANT | REGISTRO GOOGLE</title>
  <style>
        * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html, body {
      background-color: #000;
      color: white;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }

    body {
      display: flex;
      justify-content: center;
      padding: 20px;
      min-height: 100vh;
    }

    .registro {
      width: 100%;
      max-width: 600px;
      padding: 30px 20px;
      background-color: #000;
      border-radius: 12px;
    }

    .tituloFlecha {
      display: inline-block;
      margin-bottom: 40px;
      position: relative;
    }

    .tituloFlecha h1 {
      font-size: 2rem;
      margin-bottom: 8px;
    }

    .tituloFlecha::after {
      content: "";
      position: absolute;
      left: 0;
      bottom: 0;
      height: 5px;
      width: 100%;
      background-color: white;
    }

    .tituloFlecha::before {
      content: "";
      position: absolute;
      bottom: -6.5px;
      right: -16px; 
      width: 0;
      height: 0;
      border-top: 10px solid transparent;
      border-bottom: 10px solid transparent;
      border-left: 16px solid white;
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
    }

    button:hover {
      background-color: #8cd6ac;
    }

    @media (max-width: 768px) {
      .tituloFlecha h1 {
        font-size: 1.6rem;
      }

      .tituloFlecha::before {
        right: -16px;
        border-left: 14px solid white;
      }
    }

    @media (max-width: 480px) {
      .tituloFlecha h1 {
        font-size: 1.4rem;
        margin-bottom: 15px;
      }

      input, button {
        font-size: 16px;
        
      }

      button {
        margin-top: 50px;
      }

      input {
        margin-bottom: 30px;
      }
      
      .tituloFlecha{
        margin-bottom: 80px;
      }
    }
  </style>
</head>
<body>
  <section class="registro">
    <div class="tituloFlecha">
      <h1>REGISTRO POR GOOGLE</h1>
    </div>

    <form method="POST" action="">
      <label for="usuario">Usuario</label>
      <input type="text" id="usuario" name="usuario" required>

      <label for="salario">Salario</label>
      <input type="number" id="salario" name="salario" required>

      <label for="ahorro">Porcentaje de ahorro</label>
      <input type="number" id="ahorro" name="ahorro" required>

      <button type="submit">Registrarse</button>
    </form>
  </section>
</body>
</html>
