<?php
require_once 'conexao.php';
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da atividade não fornecido']);
    exit;
}

$id = $_GET['id'];

try {
    $atividade = R::getRow("
        SELECT 
            a.*,
            u.nome as professor_nome
        FROM atividade a
        INNER JOIN usuario u ON a.criado_por = u.id
        WHERE a.id = ?
    ", [$id]);

    if ($atividade) {
        // Adicionar cor hexadecimal
        echo json_encode($atividade);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Atividade não encontrada']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar detalhes: ' . $e->getMessage()]);
}

function numeroParaCor($numero)
{
    $cores = [
        1 => '#3b82f6', // Azul
        2 => '#ef4444', // Vermelho
        3 => '#10b981', // Verde
        4 => '#f59e0b', // Amarelo
        5 => '#8b5cf6', // Roxo
        6 => '#ec4899', // Rosa
        7 => '#06b6d4', // Ciano
        8 => '#84cc16', // Lima
    ];

    return $cores[$numero] ?? '#3b82f6';
}
