<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Obtener el mes y año actual por defecto
$currentMonth = date("m");
$currentYear = date("Y");

// Obtener mes y año del formulario (si están seleccionados)
$mes = isset($_GET['mes']) ? $_GET['mes'] : $currentMonth;
$anio = isset($_GET['anio']) ? $_GET['anio'] : $currentYear;

// Crear la consulta base
$query = "SELECT * FROM gastos WHERE usuario_id = :usuario_id";

// Filtrar por mes y año si están definidos
if ($mes && $anio) {
    $query .= " AND MONTH(fecha) = :mes AND YEAR(fecha) = :anio";
} elseif ($mes) {
    $query .= " AND MONTH(fecha) = :mes";
} elseif ($anio) {
    $query .= " AND YEAR(fecha) = :anio";
}

// Ordenar por fecha
$query .= " ORDER BY fecha DESC";

// Preparar la consulta
$stmt = $conn->prepare($query);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);

// Vincular los parámetros de mes y año si están definidos
if ($mes) {
    $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
}
if ($anio) {
    $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
}

$stmt->execute();
$gastos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
  
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CASH ANT | HISTORIAL</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <link rel="stylesheet" href="cssMenu.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      font-family: sans-serif;
      background-color: black;
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

    h1 {
      margin-right: 870px;
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
      right: -10px;
      width: 0;
      height: 0;
      border-top: 6px solid transparent;
      border-bottom: 6px solid transparent;
      border-left: 16px solid #fff;
    }

    .table-wrapper {
      width: 100%;
      overflow-x: auto;
    }

    .custom-table {
      width: 80%;
      margin: 0 auto;
      font-size: 0.95rem;
      white-space: nowrap;
      min-width: 800px;
      border-collapse: collapse;
      color: #eee;
      background-color: #000;
      margin-bottom: 150px;
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

    @media (min-height: 650px) and (max-height: 699px) {
      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 1.5rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }

      h1 {
        margin-right: 140px;
        font-size: 2.5rem;
      }

      .flecha-subrayado {
        font-size: 40px;
        margin-bottom: 50px;
      }
    }

    @media (min-height: 700px) and (max-height: 799px) {
      h1 {
        margin-right: 68px;
        font-size: 2.5rem !important;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 1.5rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 700px) and (max-height: 799px) and (min-width: 361px){
      h1 {
        margin-right: 230px;
        font-size: 2.5rem !important;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 1.5rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 800px) and (max-height: 899px) {
      h1 {
        margin-right: 50px;
        font-size: 3.3rem !important;
        margin-top: 55px;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 2.3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 800px) and (max-height: 899px) and (max-width: 399px){
      h1 {
        margin-right: 25px;
        font-size: 3.3rem !important;
        margin-top: 55px;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 2.3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 800px) and (max-height: 899px) and (max-width: 350px){
      h1 {
        margin-right: -20px;
        font-size: 3.3rem !important;
        margin-top: 55px;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 2.3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 900px) and (max-height: 999px){
      h1 {
        margin-right: 45px;
        font-size: 3.5rem !important;
        margin-top: 60px;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 2.3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 900px) and (max-height: 999px) and (max-width: 415px){
      h1 {
        margin-right: 28px;
        font-size: 3.5rem !important;
        margin-top: 60px;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 2.3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 1000px) and (max-height: 1099px){
      h1 {
        margin-right: 300px;
        font-size: 4.5rem !important;
        margin-top: 20px;
        margin-bottom: 70px !important;
      }

      .custom-table {
        margin-left: 40px;  
        margin-right: 40px; 
        font-size: 2rem;
        padding-bottom: 50px;
        margin-bottom: 290px !important;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 1101px) and (max-height: 1199px){
      h1 {
        margin-right: 295px;
        font-size: 4.5rem !important;
        margin-top: 70px;
        margin-bottom: 90px !important;
      }

      .custom-table {
        margin-left: 60px;  
        margin-right: 40px; 
        font-size: 2rem;
        padding-bottom: 50px;
        margin-bottom: 290px;
      }

      th, td {
        padding: 0.5rem !important;
      }

      .filtroForm {
        margin-top: -50px;
      }
    }

    @media (min-height: 1300px) and (max-width: 1000px){
      h1 {
        margin-right: 220px !important;
        font-size: 5.5rem !important;
        margin-top: 130px;
        margin-bottom: 100px !important;
      }

      .custom-table {
        margin-left: 110px;  
        margin-right: 50px; 
        font-size: 3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }
    
    @media (min-height: 1300px) and (min-width: 1000px) and (max-width: 1050px){
      h1 {
        margin-right: 300px !important;
        font-size: 5.5rem !important;
        margin-top: 105px;
        margin-bottom: 100px !important;
      }

      .custom-table {
        margin-left: 110px;  
        margin-right: 50px; 
        font-size: 3rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-height: 1200px) and (max-width: 1299px){
      h1 {
        margin-right: 155px;
        font-size: 5.5rem !important;
        margin-top: 105px;
        margin-bottom: 100px !important;
      }

      .custom-table {
        margin-left: 110px;  
        margin-right: 50px; 
        font-size: 2rem;
        padding-bottom: 50px;
      }

      th, td {
        padding: 0.5rem !important;
      }

      .filtroForm {
        margin-top: -50px;
      }
    }

    @media (min-width: 1000px) and (max-width: 1200px) and (min-height: 599px) and (max-height: 650px){
      h1 {
        margin-right: 550px;
        font-size: 3rem !important;
        margin-top: 20px;
        margin-bottom: 40px !important;
      }

      .custom-table {
        margin-left: 110px;  
        margin-right: 50px; 
        font-size: 1.5rem;
        padding-bottom: 50px;
        margin-bottom: 200px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (min-width: 1200px) {
      h1{
        margin-right: 825px;
        font-size: 3.5rem !important;
        margin-top: 35px;
        margin-bottom: 45px !important;
      }

      .custom-table {
        margin-left: 110px;  
        margin-right: 50px; 
        font-size: 1rem;
        padding-bottom: 50px;
        margin-bottom: 200px;
      }

      th, td {
        padding: 0.5rem !important;
      }
    }

    @media (max-width: 576px) {
      .flecha-subrayado {
        font-size: 2rem;
        margin-right: 80px;
      }

      .custom-table {
        margin-bottom: 180px;
        font-size: 1rem;
      }
    }

    @media (min-height: 1001px) and (max-height: 1299px) {
      .homecirculo {
        width: 150px;
        height: 150px;
        bottom: 70px;
      }

      .circuloContent img {
        width: 115px;
        height: 115px;
      }

      .menuItem i {
        font-size: 55px;
      }
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

    @media (max-width: 767px) {
      .filtroForm {
        flex-direction: column;
        gap: 15px;
        font-size: 2px;
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

      .filtroForm label {
        font-size: 20px;
      }

    }
  </style>
</head>
<body>
  <div class="tituloContainer">
    <h1 class="flecha-subrayado">HISTORIAL</h1>
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

  <div class="container mb-5">
    <div class="table-wrapper">
      <table class="custom-table text-center">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Descripción</th>
            <th>Monto</th>
            <th>Categoría</th>
          </tr>
        </thead>
        <tbody id="tablaHistorial">
          <?php foreach ($gastos as $gasto): ?>
            <tr>
              <td><?php echo htmlspecialchars($gasto['fecha']); ?></td>
              <td><?php echo htmlspecialchars($gasto['descripcion']); ?></td>
              <td><?php echo number_format($gasto['monto'], 2); ?>€</td>
              <td><?php echo htmlspecialchars($gasto['categoria']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
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
