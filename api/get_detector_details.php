<?php
require_once 'db_connect.php';

// Obtém o nome do detector da URL (GET)
$detector_name = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';

if (empty($detector_name)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Nome do detector não fornecido."));
    $conn->close();
    exit();
}

// Busca o detector
$sql = "SELECT detector_id, name, cep, current_status, 
               DATE_FORMAT(last_update, '%d/%m/%Y %H:%i:%s') as lastUpdate, 
               precision_level 
        FROM Detectors 
        WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $detector_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $detector = $result->fetch_assoc();
    // Ajusta o nome do campo para o JS
    $detector['precision'] = $detector['precision_level']; 
    unset($detector['precision_level']); 
    
    echo json_encode(array("success" => true, "detector" => $detector));
} else {
    echo json_encode(array("success" => false, "message" => "Detector não encontrado."));
}

$stmt->close();
$conn->close();
?>