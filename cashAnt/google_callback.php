<?php
require_once 'conexion.php';
session_start();

// CONFIGURACIÓN GOOGLE
$client_id = "947810257602-tg6uflbl1hfntbu5cllf0dhb23dm6ain.apps.googleusercontent.com";
$client_secret = "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";
$redirect_uri = "http://localhost/preCash/google_callback.php";

if (!isset($_GET['code'])) {
    die("No se recibió ningún código de Google.");
}

// Obtener código
$code = $_GET['code'];

// Intercambiar por token
$token_url = "https://oauth2.googleapis.com/token";
$data = [
    'code' => $code,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code'
];

$options = [
    "http" => [
        "header" => "Content-Type: application/x-www-form-urlencoded",
        "method" => "POST",
        "content" => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($token_url, false, $context);

if ($response === FALSE) {
    die("Error al obtener el token de acceso.");
}

$result = json_decode($response, true);

if (!isset($result['access_token'])) {
    die("No se pudo obtener el token de acceso.");
}

$access_token = $result['access_token'];

// Obtener datos del usuario
$profile_url = "https://www.googleapis.com/oauth2/v3/userinfo";
$options = [
    "http" => [
        "header" => "Authorization: Bearer " . $access_token,
        "method" => "GET"
    ]
];

$context = stream_context_create($options);
$profile_response = file_get_contents($profile_url, false, $context);
$profile_data = json_decode($profile_response, true);

$email = $profile_data['email'];
$nombre = $profile_data['name'] ?? '';
$given_name = $profile_data['given_name'] ?? '';
$family_name = $profile_data['family_name'] ?? '';
$picture = $profile_data['picture'] ?? '';

// Guardar en sesión
$_SESSION['usuario'] = $email;
$_SESSION['nombre'] = $nombre;
$_SESSION['foto'] = $picture;

// Verificar si ya existe
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Usuario ya registrado
    header("Location: home.php");
    exit;
} else {
    // Usuario nuevo → pedir completar datos
    header("Location: registroG.php");
    exit;
}
