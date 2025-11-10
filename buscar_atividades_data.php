<?php
require_once 'conexao.php';
header('Content-Type: application/json');

if (!isset($_GET['data']) || !isset($_GET['turma_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Parâmetros incompletos']);
    exit;
}

$data = $_GET['data'];
$turma_id = $_GET['turma_id'];

try {
    $atividades = R::getAll("
        SELECT 
            a.idatividade,
            a.titulo,
            a.descricao,
            a.horario,
            t.nome as tipo_nome,
            d.nome as disciplina_nome,
            d.cor as cor_numero,
            u.nome as professor_nome
        FROM atividade a
        INNER JOIN tipo t ON a.tipo_idtipo = t.idtipo
        INNER JOIN disciplina d ON a.disciplina_iddisciplina = d.iddisciplina
        INNER JOIN usuario u ON a.professor = u.idusuario
        WHERE DATE(a.horario) = ?
        AND a.turma_idturma = ?
        ORDER BY a.horario
    ", [$data, $turma_id]);
    
    // Adicionar cor hexadecimal
    foreach ($atividades as &$atividade) {
        $atividade['cor_hex'] = numeroParaCor($atividade['cor_numero']);
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