<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data->user_id) || !isset($data->detector_name)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Dados de exclusão incompletos."));
    $conn->close();
    exit();
}

$user_id = intval($data->user_id);
$detector_name = $conn->real_escape_string($data->detector_name);

// Transação para garantir que leituras e o detector sejam excluídos
$conn->begin_transaction();
try {
    // 1. Encontrar o ID do detector e garantir que pertença ao usuário
    $select_id_sql = "SELECT detector_id FROM Detectors WHERE user_id = ? AND name = ?";
    $select_id_stmt = $conn->prepare($select_id_sql);
    $select_id_stmt->bind_param("is", $user_id, $detector_name);
    $select_id_stmt->execute();
    $result = $select_id_stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception("Detector não encontrado ou não pertence a este usuário.");
    }
    $detector = $result->fetch_assoc();
    $detector_id = $detector['detector_id'];
    $select_id_stmt->close();

    // 2. Excluir todas as leituras de gás (Obrigatório devido à FOREIGN KEY)
    $delete_readings_sql = "DELETE FROM Gas_Readings WHERE detector_id = ?";
    $delete_readings_stmt = $conn->prepare($delete_readings_sql);
    $delete_readings_stmt->bind_param("i", $detector_id);
    $delete_readings_stmt->execute();
    $delete_readings_stmt->close();

    // 3. Excluir o detector
    $delete_detector_sql = "DELETE FROM Detectors WHERE detector_id = ?";
    $delete_detector_stmt = $conn->prepare($delete_detector_sql);
    $delete_detector_stmt->bind_param("i", $detector_id);
    $delete_detector_stmt->execute();
    $delete_detector_stmt->close();
    
    $conn->commit();
    echo json_encode(array("success" => true, "message" => "Detector e leituras excluídos com sucesso."));

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Falha na exclusão: " . $e->getMessage()));
}

$conn->close();
?>