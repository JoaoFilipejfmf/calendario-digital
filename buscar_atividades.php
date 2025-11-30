<?php
require_once 'conexao.php';
header('Content-Type: application/json');

session_start();

$turma = isset($_SESSION['turma_atual']) ? $_SESSION['turma_atual'] : null;

if (!$turma) {
    echo json_encode(['error' => 'coisa boa num é']);
    exit;
}

try {
    $atividades = R::getAll("
        SELECT 
            a.id,
            a.titulo,
            a.tipo,
            a.disciplina,
            a.descricao,
            a.horario_inicio,
            a.horario_fim,
            u.nome as professor_nome
        FROM atividade a
        INNER JOIN usuario u ON a.criado_por = u.id
        WHERE a.turma = ?
        ORDER BY a.horario_inicio
    ", [$turma['id']]);
    
    echo json_encode($atividades);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar atividades: ' . $e->getMessage()]);
}

function numeroParaCor($numero) {
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
?>