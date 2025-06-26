<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

echo "Archivo cargado correctamente.<br>";

echo "Usuario ID en sesión: ";
if (isset($_SESSION['usuario_id'])) {
    echo $_SESSION['usuario_id'] . "<br>";
} else {
    echo "No hay usuario en sesión.<br>";
    exit();
}

require_once 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

$sql_usuario = "SELECT salario_base, porcentaje_gastos FROM usuarios WHERE id = ?";
$stmt_usuario = $conn->prepare($sql_usuario);
$stmt_usuario->execute([$usuario_id]);
$usuario_data = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario_data) {
    echo "No se encontró usuario en la base de datos.<br>";
    exit();
}

echo "Salario base: " . $usuario_data['salario_base'] . "<br>";
echo "Porcentaje gastos: " . $usuario_data['porcentaje_gastos'] . "<br>";

$salario_base = $usuario_data['salario_base'];
$porcentaje_gasto = $usuario_data['porcentaje_gastos'];

if (empty($porcentaje_gasto) || !is_numeric($porcentaje_gasto)) {
    $porcentaje_gasto = 0.1; 
} elseif ($porcentaje_gasto > 1) {
    $porcentaje_gasto = $porcentaje_gasto / 100;
}

$monto_destinado = $salario_base * $porcentaje_gasto;

echo "Monto destinado (salario_base * porcentaje_gasto): $monto_destinado<br>";

exit();
