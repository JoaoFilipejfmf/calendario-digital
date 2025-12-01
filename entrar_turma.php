<?php
require_once 'conexao.php';
header('Content-Type: application/json');

session_start();

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não logado']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['codigo'])) {
    echo json_encode(['success' => false, 'error' => 'O código da turma não foi fornecido.']);
    exit;
}

$codigo = trim($input['codigo']);

try {
    $participacaoturma = R::dispense('participacaoturma');

    $turma = R::findOne('turma', 'codigo = ?', [$codigo]);

    if (!$turma) {
        echo json_encode(['success' => false, 'error' => 'Código da turma inválido.']);
        exit;
    }

    $participacao_existente = R::findOne(
        'participacaoturma',
        'usuario_id = ? AND turma_id = ?',
        [$_SESSION['usuario']->id, $turma->id]
    );

    if ($participacao_existente) {
        echo json_encode(['success' => false, 'error' => 'O usuário já está registrado nesta turma.']);
        exit;
    }

    $_SESSION['turma_atual'] = $turma;

    $participacaoturma->usuario_id = $_SESSION['usuario']->id;
    $participacaoturma->turma_id = $turma['id'];
    $participacaoturma->administrador = false;

    R::store($participacaoturma);

    echo json_encode([
        'success' => true,
        'message' => 'Você entrou na turma com sucesso!'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao entrar na turma: ' . $e->getMessage()]);
}
