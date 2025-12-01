<?php
// excluir_atividade.php
require_once "conexao.php";
header('Content-Type: application/json');
session_start();

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!isset($_SESSION['usuario']) || empty($data['id'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso inválido.']);
    exit;
}

try {
    $atividade = R::load('atividade', $data['id']);

    if (!$atividade->id) {
        throw new Exception("Atividade não encontrada.");
    }

    // Verifica permissão (Dono ou Admin)
    $isDono = $atividade->criado_por == $_SESSION['usuario']['id'];
    $isAdmin = $_SESSION['turma_atual']['is_admin'] ?? false;

    if (!$isDono && !$isAdmin) {
        throw new Exception("Sem permissão para excluir.");
    }

    R::trash($atividade);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
