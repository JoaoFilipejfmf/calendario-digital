<?php
require_once 'conexao.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não logado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$turma_id = $input['turma_id'] ?? null;

if (!$turma_id) {
    echo json_encode(['success' => false, 'error' => 'ID da turma não fornecido']);
    exit;
}

// Verificar se o usuário pertence a esta turma
$pertence_turma = R::getRow("
    SELECT 1 
    FROM usuario_participa_de_turma 
    WHERE usuario_idusuario = ? AND turma_idturma = ?
", [$_SESSION['usuario_id'], $turma_id]);

if (!$pertence_turma) {
    echo json_encode(['success' => false, 'error' => 'Você não pertence a esta turma']);
    exit;
}

// Atualizar a turma atual na sessão
$_SESSION['turma_atual_id'] = $turma_id;

echo json_encode(['success' => true]);
?>