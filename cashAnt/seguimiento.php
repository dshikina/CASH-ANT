<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql_usuario = "SELECT salario_base, porcentaje_gastos FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->execute([$usuario_id]);
$usuario_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario_data) {
    die("Error: No se encontró el usuario.");
}

$salario_base = floatval($usuario_data['salario_base']);
$porcentaje_gasto = floatval($usuario_data['porcentaje_gastos']);

if ($porcentaje_gasto > 1) {
    $porcentaje_gasto /= 100;
}

if ($salario_base <= 0 || $porcentaje_gasto <= 0) {
    die("Error: El salario base o porcentaje de gastos es inválido.");
}
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');

$sql = "SELECT DAY(fecha) as dia, SUM(monto) as total_gasto 
        FROM gastos 
        WHERE usuario_id = ? 
        AND MONTH(fecha) = ? 
        AND YEAR(fecha) = ?
        GROUP BY dia 
        ORDER BY dia ASC";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario_id, $mes, $anio]);
$gastos_por_dia = $stmt->fetchAll(PDO::FETCH_ASSOC);

$dias = range(1, 30);  
$gastos_diarios = array_fill(0, 30, 0);

foreach ($gastos_por_dia as $gasto) {
    $index = intval($gasto['dia']) - 1;
    if ($index >= 0 && $index < 30) {
        $gastos_diarios[$index] = floatval($gasto['total_gasto']);
    }
}

$monto_destinado = $salario_base * $porcentaje_gasto;
$total_gasto = array_sum($gastos_diarios);
$ahorrado = $monto_destinado - $total_gasto;
$porcentaje_gasto_actual = ($total_gasto / $salario_base) * 100;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CASH ANT | BALANCE</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="cssMenu.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: sans-serif;
    }

    body {
      display: flex;
      flex-direction: column;
      background-color: #000;
    }

    h1 {
      margin: 0 auto 40px auto;
      text-align: center;
      color: white;
    }

    .filtroContainer {
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: 40px;
      margin-bottom: 30px;
    }

    .filtroForm {
      display: flex;
      flex-direction: row;
      gap: 20px;
      align-items: center;
      background-color: #2d2d2d;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
    }

    .selectWrapper {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .filtroSelect {
      padding: 8px;
      background-color: #1c1c1c;
      color: #fff;
      border: none;
      border-radius: 8px;
      width: 120px;
      font-size: 1rem;
      transition: background-color 0.3s ease;
    }

    .filtroSelect:hover {
      background-color: #444;
    }

    .filtroButton {
      padding: 10px 20px;
      background-color: #8CC9AD;
      color: #000;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .filtroButton:hover {
      background-color: #8CC9AD;
    }

    @media (max-width: 768px) {
      .filtroForm {
        flex-direction: column;
        gap: 15px;
      }

      .filtroButton {
        width: 100%;
      }

      .selectWrapper {
        align-items: flex-start;
      }

      .filtroSelect, .filtroButton {
        width: 100%;
      }
    }

    .tituloContainer {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 40px 20px 20px;
      text-align: center;
    }

    .flecha-subrayado {
      position: relative;
      display: inline-block;
      color: white;
      font-size: 3rem;
      padding-bottom: 10px;
      margin-bottom: 40px;
    }

    .flecha-subrayado::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      height: 5px;
      width: 100%;
      background: #fff;
    }

    .flecha-subrayado::before {
      content: "";
      position: absolute;
      bottom: -3px;
      right: 0;
      width: 0;
      height: 0;
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      border-left: 8px solid #fff;
    }

    .table-wrapper {
      width: 100%;
      overflow-x: auto;
      margin-bottom: 60px;
    }

    .custom-table {
      width: 92%;
      margin-top: 50  px;
      margin-left: 40px;
      font-size: 0.95rem;
      white-space: nowrap;
      min-width: 600px;
      border-collapse: collapse;
      color: #eee;
      background-color: #000;
      margin-bottom: 30px;
    }

    .custom-table th {
      background-color: rgb(66, 151, 219);
      color: #fff;
      border: 1px solid #444;
      padding: 10px;
    }

    .custom-table td {
      background-color: #96d2b1;
      border: 1px solid #444;
      padding: 10px;
      color: #000;
    }

    @media (min-height: 800px) {
      .custom-table {
        width: 92%;
        margin-top: 100px;
        margin-left: 40px;
        font-size: 0.95rem;
        white-space: nowrap;
        min-width: 600px;
        border-collapse: collapse;
        color: #eee;
        background-color: #000;
        margin-bottom: 30px;
      }
    }
  </style>
</head>
<body>
  <div class="tituloContainer">
    <h1 class="flecha-subrayado">SEGUIMIENTO</h1>
  </div>

  <div class="filtroContainer">
    <form method="GET" action="" class="filtroForm">
      <div class="selectWrapper">
        <label for="mes" style="color: white">Mes: </label>
        <select name="mes" id="mes" class="filtroSelect">
          <option value="1" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 1) ? 'selected' : ''; ?>>Enero</option>
          <option value="2" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 2) ? 'selected' : ''; ?>>Febrero</option>
          <option value="3" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 3) ? 'selected' : ''; ?>>Marzo</option>
          <option value="4" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 4) ? 'selected' : ''; ?>>Abril</option>
          <option value="5" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 5) ? 'selected' : ''; ?>>Mayo</option>
          <option value="6" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 6) ? 'selected' : ''; ?>>Junio</option>
          <option value="7" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 7) ? 'selected' : ''; ?>>Julio</option>
          <option value="8" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 8) ? 'selected' : ''; ?>>Agosto</option>
          <option value="9" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 9) ? 'selected' : ''; ?>>Septiembre</option>
          <option value="10" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 10) ? 'selected' : ''; ?>>Octubre</option>
          <option value="11" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 11) ? 'selected' : ''; ?>>Noviembre</option>
          <option value="12" <?php echo (isset($_GET['mes']) && $_GET['mes'] == 12) ? 'selected' : ''; ?>>Diciembre</option>
        </select>
      </div>

      <div class="selectWrapper">
        <label for="anio" style="color: white">Año: </label>
        <select name="anio" id="anio" class="filtroSelect">
          <?php
            $currentYear = date("Y");
            for ($i = $currentYear; $i >= 2000; $i--) {
              echo "<option value='$i' " . (isset($_GET['anio']) && $_GET['anio'] == $i ? 'selected' : '') . ">$i</option>";
            }
          ?>
        </select>
      </div>

      <button type="submit" class="filtroButton">Filtrar</button>
    </form>
  </div>

  <div class="bodyBalance">
    <div class="container mb-5">
      <div class="table-wrapper">
        <table class="custom-table">
          <thead>
            <tr>
              <th>Monto destinado</th>
              <th>Gastado</th>
              <th>% Gastado del salario base</th>
              <th>Ahorrado</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?= number_format($monto_destinado, 2) ?> €</td>
              <td><?= number_format($total_gasto, 2) ?> €</td>
              <td><?= number_format($porcentaje_gasto_actual, 2) ?>%</td>
              <td><?= number_format($ahorrado, 2) ?> €</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

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
</body>
</html>