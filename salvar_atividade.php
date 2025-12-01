<?php
require_once "conexao.php";
header('Content-Type: application/json');
session_start();

// ... (Suas validações de sessão e recebimento de JSON continuam iguais) ...
if (!isset($_SESSION['usuario']) || !isset($_SESSION['turma_atual'])) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) { echo json_encode(['success' => false, 'error' => 'Dados inválidos.']); exit; }

try {
    // Variáveis auxiliares para a verificação
    $turmaId = $_SESSION['turma_atual']['id'];
    $dataAtividade = $data['data'];
    $idAtual = $data['id'] ?? null; // ID da atividade se for edição, ou null se for criação

    // ==================================================================================
    // 🛑 BLOQUEIO DE LIMITES (DIÁRIO E SEMANAL)
    // ==================================================================================

    // 1. VERIFICAÇÃO DIÁRIA (Máx 3)
    // Query base: atividades desta turma nesta data
    $sqlDay = 'turma = ? AND data_atividade = ?';
    $paramsDay = [$turmaId, $dataAtividade];

    // Se for edição, exclui a própria atividade da contagem para não se contar duas vezes
    if ($idAtual) {
        $sqlDay .= ' AND id != ?';
        $paramsDay[] = $idAtual;
    }

    $qtdNoDia = R::count('atividade', $sqlDay, $paramsDay);

    if ($qtdNoDia >= 3) {
        throw new Exception("Limite diário atingido! Já existem 3 atividades nesta data.");
    }

    // 2. VERIFICAÇÃO SEMANAL (Máx 10)
    // Calcula o início (Segunda) e fim (Domingo) da semana baseada na data da atividade
    $dataObj = new DateTime($dataAtividade);
    
    // Se hoje não for segunda-feira (1), volta para a última segunda
    if ($dataObj->format('N') != 1) {
        $dataObj->modify('last monday');
    }
    $inicioSemana = $dataObj->format('Y-m-d');
    
    // Avança 6 dias para pegar o domingo
    $dataObj->modify('+6 days'); 
    $fimSemana = $dataObj->format('Y-m-d');

    // Query: atividades desta turma dentro do intervalo da semana
    $sqlWeek = 'turma = ? AND data_atividade BETWEEN ? AND ?';
    $paramsWeek = [$turmaId, $inicioSemana, $fimSemana];

    if ($idAtual) {
        $sqlWeek .= ' AND id != ?';
        $paramsWeek[] = $idAtual;
    }

    $qtdNaSemana = R::count('atividade', $sqlWeek, $paramsWeek);

    if ($qtdNaSemana >= 7) {
        throw new Exception("Limite semanal atingido! Já existem 7 atividades nesta semana ({$inicioSemana} a {$fimSemana}).");
    }

    // ==================================================================================
    // ✅ FIM DO BLOQUEIO - PROSSEGUE COM O SALVAMENTO
    // ==================================================================================

    // Lógica de Edição vs Criação (Seu código anterior continua aqui...)
    if ($idAtual) {
        $atividade = R::load('atividade', $idAtual);
        // ... verificações de dono/admin ...
         if (!$atividade->id) throw new Exception("Atividade não encontrada.");
         // Validação de permissão...
    } else {
        $atividade = R::dispense('atividade');
        $atividade->criado_por = $_SESSION['usuario']['id'];
        $atividade->created_at = R::isoDateTime();
    }

    // ... (Restante do preenchimento e R::store continua igual) ...
    $atividade->titulo = $data['titulo'];
    $atividade->tipo = $data['tipo'];
    $atividade->disciplina = $data['disciplina'];
    $atividade->data_atividade = $data['data'];
    $atividade->descricao = $data['descricao'] ?? null;
    $atividade->turma = $turmaId;

    // Tratamento de horário
    if (!empty($data['inicio'])) {
        $atividade->horario_inicio = $data['data'] . ' ' . $data['inicio'] . ':00';
    } else {
        $atividade->horario_inicio = null;
    }
    if (!empty($data['fim'])) {
        $atividade->horario_fim = $data['data'] . ' ' . $data['fim'] . ':00';
    } else {
        $atividade->horario_fim = null;
    }

    $id = R::store($atividade);

    echo json_encode(['success' => true, 'message' => 'Atividade salva com sucesso!', 'id' => $id]);

} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>