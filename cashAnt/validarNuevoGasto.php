<?php
session_start();
require_once 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $importe = $_POST['importe'] ?? null;
    $concepto = $_POST['concepto'] ?? null;
    $categoria = $_POST['categoria'] ?? null;

    if (empty($importe) || empty($concepto) || empty($categoria)) {
        echo "Todos los campos son obligatorios.";
        exit();
    }

    $importe = trim($importe);
    $importe = str_replace(',', '.', $importe);
    $importe = preg_replace('/[^0-9.]/', '', $importe);

    if (substr_count($importe, '.') > 1) {
        echo "Formato de importe inválido.";
        exit();
    }

    if (!is_numeric($importe)) {
        echo "El importe ingresado no es válido.";
        exit();
    }

    $importe = floatval($importe);

    if ($importe <= 0) {
        echo "El importe debe ser un número positivo.";
        exit();
    }

    $importe_formateado = number_format($importe, 2, '.', '');

    $query = "INSERT INTO gastos (usuario_id, fecha, descripcion, monto, categoria) 
              VALUES (:usuario_id, CURDATE(), :descripcion, :monto, :categoria)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':descripcion', $concepto, PDO::PARAM_STR);
    $stmt->bindValue(':monto', $importe_formateado, PDO::PARAM_STR);
    $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // NO hacer header redireccionando
        echo '<script>
            window.parent.postMessage("gasto-registrado", "*");
        </script>';
        exit();
    }

} else {
    echo "Acción no permitida.";
    exit();
}
