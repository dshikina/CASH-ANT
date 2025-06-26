<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  http_response_code(403);
  echo json_encode(['error' => 'No autorizado']);
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
$ahorro_deseado_inicial = $salario * ($porcentaje_gastos / 100);
$ahorro_actual = max($ahorro_deseado_inicial - $total_gastos, 0);
$porcentaje_gasto = $ahorro_deseado_inicial > 0 ? min(($total_gastos / $ahorro_deseado_inicial) * 100, 100) : 0;

header('Content-Type: application/json');
echo json_encode([
  'ahorro_actual' => $ahorro_actual,
  'porcentaje_gasto' => $porcentaje_gasto
]);
