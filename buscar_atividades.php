<?php
require_once 'conexao.php';
header('Content-Type: application/json');

$turma_id = isset($_GET['turma_id']) ? $_GET['turma_id'] : null;

if (!$turma_id) {
    echo json_encode([]);
    exit;
}

try {
    $atividades = R::getAll("
        SELECT 
            a.idatividade as id,
            a.titulo,
            DATE(a.horario) as start,
            d.cor as color,
            t.nome as tipo_nome,
            d.nome as disciplina_nome
        FROM atividade a
        INNER JOIN tipo t ON a.tipo_idtipo = t.idtipo
        INNER JOIN disciplina d ON a.disciplina_iddisciplina = d.iddisciplina
        WHERE a.turma_idturma = ?
        AND a.horario >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
        ORDER BY a.horario
    ", [$turma_id]);
    
    // Converter cor numérica para hexadecimal
    foreach ($atividades as &$atividade) {
        $atividade['color'] = numeroParaCor($atividade['color']);
    }
    
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