<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit();
}

$usuario_id = $_SESSION['usuario_id'];

$sql = "SELECT salario_base, porcentaje_gastos FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$salario = floatval(str_replace(',', '.', $usuario['salario_base'] ?? '0.00'));
$porcentaje_gastos = $usuario['porcentaje_gastos'] ?? 0;

$sqlGastos = "SELECT SUM(monto) as total_gastos 
              FROM gastos 
              WHERE usuario_id = ? 
              AND MONTH(fecha) = MONTH(CURDATE()) 
              AND YEAR(fecha) = YEAR(CURDATE())";
$stmtGastos = $conn->prepare($sqlGastos);
$stmtGastos->execute([$usuario_id]);
$resultadoGastos = $stmtGastos->fetch(PDO::FETCH_ASSOC);

$total_gastos = floatval(str_replace(',', '.', $resultadoGastos['total_gastos'] ?? '0.00'));

$user = $_SESSION['user'] ?? 'Usuario';

$ahorro_deseado_inicial = $salario * ($porcentaje_gastos / 100);

$ahorro_actual = max($ahorro_deseado_inicial - $total_gastos, 0);

$porcentaje_gasto = $ahorro_deseado_inicial > 0
    ? min(($total_gastos / $ahorro_deseado_inicial) * 100, 100)
    : 0;

$sqlGastosCategorias = "SELECT categoria, SUM(monto) as total_categoria 
                        FROM gastos 
                        WHERE usuario_id = ? 
                        AND MONTH(fecha) = MONTH(CURDATE()) 
                        AND YEAR(fecha) = YEAR(CURDATE()) 
                        GROUP BY categoria";  

$stmtGastosCategorias = $conn->prepare($sqlGastosCategorias);
$stmtGastosCategorias->execute([$usuario_id]);
$resultadoCategorias = $stmtGastosCategorias->fetchAll(PDO::FETCH_ASSOC);

$categoriasContadas = [];

foreach ($resultadoCategorias as $categoria) {
    $categoria_nombre = $categoria['categoria'];
    
    if (isset($categoriasContadas[$categoria_nombre])) {
        $categoriasContadas[$categoria_nombre]++;
    } else {
        $categoriasContadas[$categoria_nombre] = 1;
    }
}
$totalCategorias = array_sum($categoriasContadas);

$porcentajesCategorias = [];

foreach ($categoriasContadas as $categoria => $cantidad) {
    $porcentaje = $totalCategorias > 0 ? ($cantidad / $totalCategorias) * 100 : 0;
    $porcentajesCategorias[$categoria] = $porcentaje;
}

$total_gastos = floatval(str_replace(',', '.', $resultadoGastos['total_gastos'] ?? '0.00'));

$porcentajesCategorias = [];

foreach ($resultadoCategorias as $categoria) {
    $categoria_nombre = $categoria['categoria'];
    $monto_categoria = floatval(str_replace(',', '.', $categoria['total_categoria'] ?? '0.00'));

    $porcentaje = $total_gastos > 0 ? ($monto_categoria / $total_gastos) * 100 : 0;
    $porcentajesCategorias[$categoria_nombre] = $porcentaje;
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CASH ANT | HOME</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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

    .headerHome {
      height: 15vh;
      background-color: #8CC9AD;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .headerHome h2 {
      font-size: 30px;
      margin-top: 15px;
    }

    .headerHome h3 {
      text-align: center;
      font-size: 15px;
      margin-top: -10px;
      width: 100%; 
    }


    .bodyHome {
      min-height: 70vh;
      max-height: calc(100vh - 15vh);
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      padding: 10px;
    }

    .barraBalance {
      width: 80%;
      margin-top: 40px;
      color: white;
    }

    .textoArribaBalance,
    .textoAbajoBalance {
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      margin: 2px 0;
    }

    .balanceMain {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 10px;
    }

    .balanceMain img {
      width: 30px;
      height: 30px;
    }

    .barContainer {
      flex-grow: 1;
      margin: 0 10px;
      background-color: #ddd;
      border-radius: 5px;
      height: 10px;
      position: relative;
      width: 100%; 
      max-width: 100%;
    }

    .bar {
      height: 10px;
      border-radius: 5px;
      width: 0%;
      background-color: green;
      transition: width 0.5s, background-color 0.5s;
    }

    .circulo {
      width: 20px;
      height: 20px;
      border-radius: 50%;
      position: absolute;
      top: -5px;
      left: 0%;
      transform: translateX(-50%);
      background-color: green;
      transition: left 0.5s, background-color 0.5s;
    }

    .grafico {
      margin: 40px 0;
    }

    .grafico img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
    }

    .nuevoGasto {
      margin-bottom: 120px;
      text-align: center;
      color: white;
    }

    .nuevoGasto button {
      background-color: #8CC9AD;
      border: none;
      padding: 10px 20px;
      font-size: 25px;
      border-radius: 8px;
      cursor: pointer;
      color: #000;
      margin-bottom: 5px;
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: #96d2b1;
      padding: 20px;
      width: 300px;
      height: 400px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
      position: relative;
    }

    .close {
      color: white;
      font-size: 20px;
      font-weight: bold;
      position: absolute;
      top: 5px;
      right: 10px;
      cursor: pointer;
    }

    @media (min-width: 769px) {
      .grafico canvas {
        width: 220px !important;
        height: 220px !important;
      }

      .nuevoGasto {
        margin-bottom: 200px;
        text-align: center;
        color: white;
      }
    }
    
    @media (min-height: 799px){
      .grafico {
        margin: 60px 0;
      }

      .nuevoGasto{
        margin-top: 35px;
      }
    }

    @media (min-height: 1300px) {
      .grafico {
        margin: 100px 0;
      }

      .nuevoGasto{
        margin-top: 20px;
      }

      .headerHome h2 {
        font-size: 65px
      }

      .headerHome h3 {
        text-align: center;
        font-size: 35px;
        margin-top: -30px;
        width: 100%;
      }

      .nuevoGasto button {
        font-size: 75px;
      }

      .nuevoGasto {
        font-size: 40px;
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
        margin-top: 0px;
      }
    }

    #graficoCircular {
      width: 220px !important;
      height: 220px !important;
    }

    @media (min-height: 1001px) and (max-height: 1299px) {
      #graficoCircular {
        width: 300px !important; 
        height: 300px !important; 
      }
    }

    @media (min-height: 1300px) {
      #graficoCircular {
        width: 400px !important; 
        height: 400px !important; 
      }
    }

    @media (min-height: 1300px) {
      .barraBalance {
        margin-top: 1em; 
      }

      .textoArribaBalance,
      .textoAbajoBalance {
        font-size: 1.5rem; 
      }

      .balanceMain img {
        width: 4em; 
        height: 4em;
      }

      .barContainer {
        height: 1em;
      }

      .bar {
        height: 1em; 
      }

      .circulo {
        width: 1.5em; 
        height: 1.5em; 
      }

      .grafico img {
        width: 12em; 
        height: 12em; 
      }

      .nuevoGasto button {
        font-size: 3rem; 
      }

      .headerHome h2 {
        font-size: 3rem; 
      }

      .headerHome h3 {
        font-size: 1.5rem; 
      }
    }

    @media (min-height: 1001px) and (max-height: 1299px) {
      .barraBalance {
        margin-top: 1em;
      }

      .textoArribaBalance,
      .textoAbajoBalance {
        font-size: 1.4rem;
      }

      .balanceMain img {
        width: 3.5em;
        height: 3.5em;
      }

      .barContainer {
        height: 0.9em;
      }

      .bar {
        height: 0.9em;
      }

      .circulo {
        width: 1.3em;
        height: 1.3em;
      }

      .grafico img {
        width: 11em;
        height: 11em;
      }

      .nuevoGasto button {
        font-size: 2.5rem;
      }

      .headerHome h2 {
        font-size: 2.5rem;
      }

      .headerHome h3 {
        font-size: 1.3rem;
      }
    }

    @media (min-height: 1100px) and (max-height: 1150px) {
      .nuevoGasto {
        margin-top: 35px
      }
    }
  </style>
</head>

<body>
  <div class="headerHome">
    <h2><?php echo htmlspecialchars($user); ?></h2>
    <h3>Dinero Restante para gastos hormigas: €<?php echo number_format($ahorro_actual, 2); ?></h3>
  </div>

  <div class="bodyHome">
    <div class="barraBalance">
      <div class="textoArribaBalance">
        <span>Saldo</span>
        <span>Ahorro</span>
      </div>

      <div class="balanceMain">
        <img src="imagenes/bb.png" alt="cerdo" />
        <div class="barContainer">
          <div class="bar" id="bar"></div>
          <div class="circulo" id="circulo"></div>
        </div>
        <img src="imagenes/rr.png" alt="Icono derecha" />
      </div>

      <div class="textoAbajoBalance">
        <span>Disponible</span>
        <span>Actual</span>
      </div>
    </div>

    <div class="grafico">
      <canvas id="graficoCircular" width="150" height="150"></canvas>
    </div>

    <div class="nuevoGasto">
      <button id="openModal">+</button>
      NUEVO GASTO
    </div>
  </div>

  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <iframe src="nuevoGasto.php" style="width:100%; height:100%; border:none;"></iframe>
    </div>
  </div>

  <script>
    let modal = document.getElementById("myModal");
    let boton = document.getElementById("openModal");
    let span = document.getElementsByClassName("close")[0];

    boton.onclick = function () {
      modal.style.display = "flex";
    };

    span.onclick = function () {
      modal.style.display = "none";
    };

    window.onclick = function (event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    };

    function actualizarDatos() {
      fetch('datos_actualizados.php')
        .then(response => response.json())
        .then(data => {
          console.log("Datos actualizados recibidos:", data);

          document.querySelector('.headerHome h3').textContent = `Dinero Restante para gastos hormigas: €${data.ahorro_actual.toFixed(2)}`;

          let bar = document.getElementById("bar");
          let circulo = document.getElementById("circulo");
          let pct = Math.min(Math.max(data.porcentaje_gasto, 0), 100);

          bar.style.width = pct + "%";
          circulo.style.left = pct + "%";

          let rojo = Math.min(255, Math.round((pct / 100) * 255));
          let verde = Math.max(0, 200 - rojo);
          let color = `rgb(${rojo}, ${verde}, 0)`;

          bar.style.backgroundColor = color;
          circulo.style.backgroundColor = color;
        })
        .catch(error => {
          console.error('Error al actualizar datos:', error);
        });
    }

    window.addEventListener('message', function (event) {
      console.log('Mensaje recibido:', event.data);
      if (event.data === 'gasto-registrado') {
        console.log('Gasto registrado detectado, actualizando...');
        modal.style.display = "none";
        actualizarDatos();
      }
    });

    let porcentaje = <?php echo json_encode(floatval(str_replace(',', '.', $porcentaje_gasto))); ?>;
    porcentaje = Math.min(Math.max(porcentaje, 0), 100);
    console.log('Porcentaje inicial:', porcentaje);

    let bar = document.getElementById("bar");
    let circulo = document.getElementById("circulo");

    bar.style.width = porcentaje + "%";
    circulo.style.left = porcentaje + "%";

    let rojo = Math.min(255, Math.round((porcentaje / 100) * 255));
    let verde = Math.max(0, 200 - rojo);
    let color = `rgb(${rojo}, ${verde}, 0)`;

    bar.style.backgroundColor = color;
    circulo.style.backgroundColor = color;
  </script>

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

const categorias = <?php echo json_encode(array_keys($categoriasContadas)); ?>;
    const porcentajes = <?php echo json_encode(array_values($porcentajesCategorias)); ?>;

    const ctx = document.getElementById('graficoCircular').getContext('2d');
    const myChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: categorias, 
        datasets: [{
          data: porcentajes, 
          backgroundColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF8C33'],
          borderColor: ['#FF5733', '#33FF57', '#3357FF', '#FF33A1', '#FF8C33'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              color: 'white'
            }
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                return `${context.label}: ${context.parsed.toFixed(2)}%`;
              }
            }
          }
        }
      }
    });

  </script>
</body>
</html>
