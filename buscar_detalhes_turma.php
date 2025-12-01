<?php
require_once 'conexao.php';
header('Content-Type: application/json');
session_start();

$usuario = $_SESSION['usuario'] ?? null;
$usuarioId = $usuario->id ?? null;
$turmaId = $_SESSION['turma_atual']['id'] ?? null;

if (!$usuarioId || !$turmaId) {
    echo json_encode(['success' => false, 'error' => 'Dados insuficientes.']);
    exit;
}

try {
    // 1. Busca dados da Turma
    $turma = R::load('turma', $turmaId);
    if (!$turma->id) throw new Exception('Turma não encontrada.');

    // 2. Verifica se o usuário logado faz parte desta turma
    // Buscamos na tabela intermediária
    $minhaParticipacao = R::findOne('participacaoturma', 'turma_id = ? AND usuario_id = ?', [$turmaId, $usuarioId]);
    
    // Se não for membro e não for o criador (caso o criador não tenha entrada na tabela pivo, o que é raro mas possível)
    if (!$minhaParticipacao && $turma->criador != $usuarioId) {
        throw new Exception('Acesso negado.');
    }

    // 3. Busca Todos os Membros com seus status (JOIN MANUAL para performance)
    // Selecionamos dados do usuário + flag de administrador da tabela pivô
    $sql = "
        SELECT 
            u.id, 
            u.nome, 
            u.email, 
            p.administrador 
        FROM usuario u
        INNER JOIN participacaoturma p ON p.usuario_id = u.id
        WHERE p.turma_id = ?
        ORDER BY u.nome ASC
    ";
    
    $rows = R::getAll($sql, [$turmaId]);

    // 4. Formata dados
    $membros = [];
    foreach ($rows as $row) {
        $membros[] = [
            'id' => $row['id'],
            'nome' => $row['nome'],
            'email' => $row['email'],
            'is_admin' => (bool)$row['administrador'],
            'is_criador' => ($row['id'] == $turma->criador)
        ];
    }

    echo json_encode([
        'success' => true,
        'turma' => $turma->export(),
        'membros' => $membros,
        'isCriador' => ($turma->criador == $usuarioId)
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>