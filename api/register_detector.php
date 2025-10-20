<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
    !isset($data->user_id) || !isset($data->name) || 
    !isset($data->latitude) || !isset($data->longitude)) 
{
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Dados de registro incompletos."));
    $conn->close();
    exit();
}

$user_id = intval($data->user_id);
$name = $conn->real_escape_string($data->name);
$cep = $conn->real_escape_string($data->cep ?? '');
$latitude = floatval($data->latitude);
$longitude = floatval($data->longitude);
$current_status = $conn->real_escape_string($data->current_status ?? 'Clean');
$precision_level = $conn->real_escape_string($data->precision_level ?? 'Alta');

// Insere o novo detector
$sql = "INSERT INTO Detectors (user_id, name, cep, latitude, longitude, current_status, precision_level) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issddss", 
    $user_id, $name, $cep, $latitude, $longitude, $current_status, $precision_level);

if ($stmt->execute()) {
    echo json_encode(array("success" => true, "message" => "Detector registrado com sucesso."));
} else {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Erro ao registrar detector: " . $stmt->error));
}

$stmt->close();
$conn->close();
?>