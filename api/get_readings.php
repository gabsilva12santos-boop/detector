<?php
require_once 'db_connect.php';

// Obtém parâmetros da URL
$detector_id = isset($_GET['detector_id']) ? intval($_GET['detector_id']) : 0;
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : '24h';

if ($detector_id <= 0) {
    http_response_code(400);
    echo json_encode(array("success" => false, "message" => "ID do detector inválido."));
    $conn->close();
    exit();
}

// --- SIMULAÇÃO DE DADOS PARA GRÁFICO (Já que não temos a tabela Gas_Readings populada) ---
$labels = [];
$data = [];

// Gera uma semente (seed) baseada no ID do detector para que o gráfico seja o mesmo
srand($detector_id * 100); 

if ($periodo === '24h') {
    $labels = ["1h", "2h", "3h", "4h", "5h", "6h", "7h", "8h", "9h", "10h", "11h", "12h", "13h", "14h", "15h", "16h", "17h", "18h", "19h", "20h", "21h", "22h", "23h", "24h"];
    for ($i = 0; $i < 24; $i++) {
        $data[] = rand(5, 30); 
    }
} else if ($periodo === 'semana') {
    $labels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    for ($i = 0; $i < 7; $i++) {
        $data[] = rand(8, 25);
    }
} 

// Retorna o JSON formatado para o Chart.js
echo json_encode(array(
    "success" => true,
    "labels" => $labels,
    "data" => $data
));

$conn->close();
?>