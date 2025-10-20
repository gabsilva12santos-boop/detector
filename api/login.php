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

// 1. Buscar o hash da senha e o ID do usuário
$sql = "SELECT user_id, password_hash FROM Users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $hashed_password = $user['password_hash'];

    // 2. Verificar a senha
    if ($password === $hashed_password) { 
        // Login bem-sucedido
        echo json_encode(array(
            "success" => true,
            "message" => "Login realizado com sucesso!",
            "user_id" => $user['user_id']
        ));
    } else {
        echo json_encode(array("success" => false, "message" => "Senha incorreta."));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Usuário não encontrado."));
}

$stmt->close();
$conn->close();
?>