<?php
require_once 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['usuario_id'];

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : null;
$apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : null;
$user = isset($_POST['usuario']) ? trim($_POST['usuario']) : null;
$email = isset($_POST['correo']) ? filter_var(trim($_POST['correo']), FILTER_VALIDATE_EMAIL) : null;
$salario_base = isset($_POST['salario_base']) ? floatval($_POST['salario_base']) : null;
$porcentaje_gastos = isset($_POST['porcentaje_gastos']) ? floatval($_POST['porcentaje_gastos']) : null;
$contrasena = isset($_POST['contrasena']) ? trim($_POST['contrasena']) : null;

if (!$email) {
    die("Correo no válido");
}

$sql = "SELECT nombre, apellido, user, email, salario_base, porcentaje_gastos FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre = $nombre ?? $userData['nombre'];
$apellido = $apellido ?? $userData['apellido'];
$user = $user ?? $userData['user'];
$email = $email ?? $userData['email'];
$salario_base = $salario_base ?? $userData['salario_base'];
$porcentaje_gastos = $porcentaje_gastos ?? $userData['porcentaje_gastos'];

if (!empty($contrasena)) {
    $hash = password_hash($contrasena, PASSWORD_BCRYPT);
    $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, user = ?, email = ?, contrasenia = ?, salario_base = ?, porcentaje_gastos = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $nombre);
    $stmt->bindParam(2, $apellido);
    $stmt->bindParam(3, $user);
    $stmt->bindParam(4, $email);
    $stmt->bindParam(5, $hash);
    $stmt->bindParam(6, $salario_base);
    $stmt->bindParam(7, $porcentaje_gastos);
    $stmt->bindParam(8, $id);
} else {
    $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, user = ?, email = ?, salario_base = ?, porcentaje_gastos = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $nombre);
    $stmt->bindParam(2, $apellido);
    $stmt->bindParam(3, $user);
    $stmt->bindParam(4, $email);
    $stmt->bindParam(5, $salario_base);
    $stmt->bindParam(6, $porcentaje_gastos);
    $stmt->bindParam(7, $id);
}

if ($stmt->execute()) {
    header("Location: ajustes.php?mensaje=Datos actualizados");
} else {
    echo "Error al actualizar: " . $stmt->errorInfo()[2];
}
?>
