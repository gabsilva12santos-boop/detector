<?php
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data->username) || !isset($data->password)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "Dados inválidos ou método incorreto."));
    $conn->close();
    exit();
}

$username = $conn->real_escape_string($data->username);
$password = $data->password;

// Usando a senha simples como o "hash" para a demonstração XAMPP
$password_hash = $password; 

// Verifica se o usuário já existe
$check_sql = "SELECT user_id FROM Users WHERE username = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $username);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo json_encode(array("success" => false, "message" => "Este nome de usuário já está em uso."));
    $check_stmt->close();
    $conn->close();
    exit();
}
$check_stmt->close();


// Insere novo usuário
$insert_sql = "INSERT INTO Users (username, password_hash) VALUES (?, ?)";
$insert_stmt = $conn->prepare($insert_sql);
$insert_stmt->bind_param("ss", $username, $password_hash);

if ($insert_stmt->execute()) {
    echo json_encode(array("success" => true, "message" => "Usuário registrado com sucesso."));
} else {
    http_response_code(500);
    echo json_encode(array("success" => false, "message" => "Erro ao registrar usuário: " . $insert_stmt->error));
}

$insert_stmt->close();
$conn->close();
?>