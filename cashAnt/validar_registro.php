<?php
require_once 'conexion.php';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $apellido = filter_input(INPUT_POST, 'apellidos', FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  
    $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);  
    $salario = filter_input(INPUT_POST, 'salario', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);  
    $gastos = filter_input(INPUT_POST, 'gastos', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);  
    $contrasena = filter_input(INPUT_POST, 'contrasena', FILTER_SANITIZE_FULL_SPECIAL_CHARS);  
    if (!empty($nombre) && !empty($apellido) && !empty($usuario) && !empty($correo) && !empty($salario) && !empty($gastos) && !empty($contrasena)) {
        try {
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :correo OR user = :usuario");
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                echo "El correo electrónico o el nombre de usuario ya están registrados.";
            } else {
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellido, user, email, contrasenia, salario_base, porcentaje_gastos) VALUES (:nombre, :apellido, :usuario, :correo, :contrasena, :salario, :gastos)");
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':contrasena', $contrasena);
                $stmt->bindParam(':salario', $salario);
                $stmt->bindParam(':gastos', $gastos);
                if ($stmt->execute()) {
                    header("Location: login.php");  
                    exit();  
                } else {
                    echo "Error al registrar el usuario.";
                }
            }
        } catch (PDOException $e) {
            echo "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        echo "Por favor, completa todos los campos.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>
