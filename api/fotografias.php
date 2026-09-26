<?php
// 1. Define que a resposta será ESTRITAMENTE em formato JSON
header('Content-Type: application/json; charset=utf-8');

// Simulação de consulta à Base de Dados (ex.: via PHP PDO)
$fotografias = [
    [
        "id" => 1,
        "titulo" => "Pôr do Sol na Praia",
        "url_imagem" => "/api/uploads/porsol.jpeg",
        "tags_ia" => ["Praia", "Pôr do Sol", "Natureza"],
        "data_upload" => "2026-09-26 14:30:00"
    ],
    [
        "id" => 2,
        "titulo" => "Sessão Urbana de Retrato",
        "url_imagem" => "api/uploads/urbano.jpeg",
        "tags_ia" => ["Pessoas", "Urbano", "Moda"],
        "data_upload" => "2026-09-26 15:10:00"
    ]
];

// 2. Define o código de estado HTTP (200 OK) e converte o array PHP para JSON
http_response_code(200);
echo json_encode([
    "status" => "sucesso",
    "total" => count($fotografias),
    "dados" => $fotografias
]);
exit;