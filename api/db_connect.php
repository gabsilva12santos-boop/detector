<?php
// Configurações do Banco de Dados XAMPP (MySQL/MariaDB)
$servername = "localhost";
$db_username = "root";     // Usuário padrão do XAMPP
$db_password = "";         // Senha padrão do XAMPP (vazio)
$dbname = "db_detecterprevine"; // O nome do banco de dados que você criou

// Cria a conexão
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    header('Content-Type: application/json');
    die(json_encode(array("success" => false, "message" => "Falha na conexão com o banco de dados: " . $conn->connect_error)));
}

// Configura o cabeçalho para CORS e JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
?>