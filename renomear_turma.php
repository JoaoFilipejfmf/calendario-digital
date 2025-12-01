<?php
require_once 'conexao.php';
header('Content-Type: application/json');
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$usuario = $_SESSION['usuario'];
$usuarioId = $usuario->id;

// Validação simples
$turmaA = R::load('turma', $data['turma_id']);

// Apenas o CRIADOR ou ADMIN pode renomear (Aqui deixei restrito ao Criador conforme pedido)
if ($turmaA->criador != $usuarioId) {
    echo json_encode(['success' => false, 'error' => 'Apenas o criador pode renomear.']);
    exit;
}

$turmaA->nome = $data['novo_nome'];

R::store($turmaA);

$sql = "
    SELECT 
        turma.*, 
        participacaoturma.administrador AS is_admin 
    FROM 
        turma
    INNER JOIN 
        participacaoturma ON participacaoturma.turma_id = turma.id 
        AND participacaoturma.usuario_id = ?
        AND participacaoturma.turma_id = ?
    ORDER BY
        turma.nome ASC
";

$parametros = [$usuario->id, $turmaA->id];

// Executa a consulta
$turmas_usuario = R::getRow($sql, $parametros);

// $turmas agora é um array de arrays associativos do PHP

$_SESSION['turma_atual'] = $turmas_usuario;
$isAdmin = $_SESSION['turma_atual']['is_admin'];
$turma = $_SESSION['turma_atual'];
$possui_turma = isset($_SESSION['turma_atual']);

echo json_encode(['success' => true]);
