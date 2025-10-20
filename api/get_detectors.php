<?php
require_once 'db_connect.php';

// Obtém o user_id da URL (GET)
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if ($user_id <= 0) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "ID do usuário não fornecido."));
    $conn->close();
    exit();
}

// Busca todos os detectores pertencentes ao usuário
$sql = "SELECT detector_id, name, latitude, longitude, current_status 
        FROM Detectors 
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$detectors = array();
while($row = $result->fetch_assoc()) {
    $detectors[] = $row;
}

// Retorna a lista de detectores
echo json_encode($detectors);

$stmt->close();
$conn->close();
?>