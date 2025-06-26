<?php
session_start();
require_once 'conexion.php';
$error = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  
    $contrasena = filter_input(INPUT_POST, 'contrasena', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  
    if (empty($usuario) || empty($contrasena)) {
        $error = 'Por favor, complete ambos campos.';
        header("Location: login.php?error=" . urlencode($error));  
        exit();
    } else {
        try {
            $stmt = $conn->prepare("SELECT id, nombre, apellido, contrasenia, user, salario_base FROM usuarios WHERE email = :usuario OR user = :usuario");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            if ($stmt->rowCount() === 1) {
                $usuarioData = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($contrasena === $usuarioData['contrasenia']) {  
                    $_SESSION['usuario_id'] = $usuarioData['id'];
                    $_SESSION['nombre'] = $usuarioData['nombre'];
                    $_SESSION['apellido'] = $usuarioData['apellido'];
                    $_SESSION['user'] = $usuarioData['user'];
                    $_SESSION['salario_base'] = $usuarioData['salario_base'];
                    header("Location: home.php");
                    exit();
                } else {
                    $error = 'Contraseña incorrecta.';
                    header("Location: login.php?error=" . urlencode($error));  
                    exit();
                }
            } else {
                $error = 'Usuario no encontrado.';
                header("Location: login.php?error=" . urlencode($error));  
                exit();
            }
        } catch (PDOException $e) {
            $error = 'Error en la base de datos: ' . $e->getMessage();
            header("Location: login.php?error=" . urlencode($error));  
            exit();
        }
    }
}
?>
