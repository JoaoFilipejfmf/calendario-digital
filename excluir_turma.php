<?php
require_once 'conexao.php';
header('Content-Type: application/json');
session_start();

$data = json_decode(file_get_contents('php://input'), true);
$usuario = $_SESSION['usuario'];
$usuarioId = $usuario->id;

$turma = R::load('turma', $data['turma_id']);

if ($turma->criador != $usuarioId) {
    echo json_encode(['success' => false, 'error' => 'Apenas o criador pode excluir a turma.']);
    exit;
}

// O RedBean e o MySQL (Foreign Keys) devem lidar com a cascata, 
// mas vamos limpar a tabela pivô manualmente para garantir, já que seu SQL usa SET NULL
R::exec('DELETE FROM participacaoturma WHERE turma_id = ?', [$turma->id]);
R::exec('DELETE FROM atividade WHERE turma = ?', [$turma->id]); // Note que no seu SQL a coluna é 'turma', não 'turma_id'
R::trash($turma);

$sql = "
    SELECT 
        turma.*, 
        participacaoturma.administrador AS is_admin 
    FROM 
        turma
    INNER JOIN 
        participacaoturma ON participacaoturma.turma_id = turma.id 
        AND participacaoturma.usuario_id = ?
    ORDER BY
        turma.nome ASC
";

$parametros = [$usuario->id];

// Executa a consulta
$turmas_usuario = R::getAll($sql, $parametros);

// $turmas agora é um array de arrays associativos do PHP

$_SESSION['turma_atual'] = $turmas_usuario[0];
$isAdmin = $_SESSION['turma_atual']['is_admin'];
$turma = $_SESSION['turma_atual'];
$possui_turma = isset($_SESSION['turma_atual']);

echo json_encode(['success' => true]);
?>