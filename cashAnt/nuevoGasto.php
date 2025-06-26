<?php
require_once 'conexion.php';

$query = "SHOW COLUMNS FROM gastos WHERE Field = 'categoria'"; 
$result = $conn->query($query);
$row = $result->fetch(PDO::FETCH_ASSOC);
$enum_values = $row['Type'];

$enum_values = str_replace("enum(", "", $enum_values);
$enum_values = str_replace(")", "", $enum_values);
$categories = explode(",", $enum_values);

foreach ($categories as $key => $category) {
    $categories[$key] = trim($category, "'");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CASH ANT | AGREGAR GASTO </title>
  <style>

    .form-box {
      background-color: #000;
      padding: 20px;
      border-radius: 8px;
    }

    .form-header {
      color: white;
      font-size: 18px;
      margin-bottom: 20px;
      position: relative;
    }

    .form-header::after {
      content: "";
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 100%;
      height: 3px;
      background-color: white;
    }

    .arrow {
      position: absolute;
      right: -6px;
      bottom: -15.4px;
      font-size: 20px;
      color: white;
    }

    label {
      color: white;
      font-size: 14px;
      display: block;
      margin-top: 15px;
    }

    input[type="text"],
    input[type="number"] {
      width: 90%;
      padding: 8px;
      margin-top: 4px;
      background-color: #1a1a1a;
      color: white;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    select {
      width: 90%;
      padding: 8px;
      margin-top: 4px;
      background-color: #1a1a1a;
      color: white;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      margin-top: 20px;
      width: 100%;
      padding: 10px;
      background-color: #a6e7c0;
      color: black;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #8cd6ac;
    }

    .form-box {
      margin-top: 30px
    }
  </style>
</head>
<body>
  <div class="card-gasto">
    <div class="form-box">
      <div class="form-header">
        Nuevo Gasto
        <span class="arrow">➔</span>
      </div>
      <form action="validarNuevoGasto.php" method="POST">
        <label for="importe">IMPORTE:</label>
        <input type="text" id="importe" name="importe" required pattern="^\d+(\.\d{1,2})?$" title="Ingrese un número válido con hasta 2 decimales, use punto para decimales"/>

        <label for="concepto">Concepto:</label>
        <input type="text" id="concepto" name="concepto" required>

        <label for="categoria">Categoría:</label>
        <select id="categoria" name="categoria" required>
          <option value="">Seleccione una categoría</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
          <?php endforeach; ?>
        </select>

        <button type="submit">Agregar</button>
      </form>
      <script>
        document.querySelector('form').addEventListener('submit', function(e) {
          let importeInput = document.getElementById('importe');
          importeInput.value = importeInput.value.replace(/,/g, '.').trim();
        });
  window.parent.postMessage('gasto-registrado', '*');

      </script>

    </div>
  </div>
</body>
</html>
