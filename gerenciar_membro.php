<?php
require_once 'conexao.php';
header('Content-Type: application/json');
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$usuarioLogado = $_SESSION['usuario'];
$usuarioId = $usuarioLogado->id;
$acao = $data['acao']; // 'remover' ou 'toggle_admin'
$alvoId = $data['usuario_id']; // O usuário alvo
$turmaId = $data['turma_id'];

$turma = R::load('turma', $turmaId);

// Segurança: Somente o criador gerencia admins e remove pessoas (exceto sair da turma)
if ($turma->criador != $usuarioId) {
    echo json_encode(['success' => false, 'error' => 'Apenas o criador tem permissão.']);
    exit;
}

// Não pode remover/alterar a si mesmo através deste endpoint
if ($alvoId == $usuarioId) {
    echo json_encode(['success' => false, 'error' => 'Você não pode alterar seu próprio status aqui.']);
    exit;
}

// Busca a linha na tabela pivô
$participacao = R::findOne('participacaoturma', 'turma_id = ? AND usuario_id = ?', [$turmaId, $alvoId]);

if (!$participacao) {
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado nesta turma.']);
    exit;
}

if ($acao == 'remover') {
    R::trash($participacao);
    echo json_encode(['success' => true, 'message' => 'Usuário removido.']);
} 
elseif ($acao == 'toggle_admin') {
    // Inverte o valor (0 vira 1, 1 vira 0)
    $participacao->administrador = !$participacao->administrador;
    R::store($participacao);
    echo json_encode(['success' => true, 'new_status' => $participacao->administrador]);
}
?>