<?php
// 1. Configurações Iniciais e Dependências
require_once "conexao.php";
header('Content-Type: application/json');

session_start();

$turma = $_SESSION['turma_atual'];
// 2. Verificações de Segurança
if (!isset($_SESSION['usuario']) || !isset($_SESSION['turma_atual']) || !$_SESSION['turma_atual']['is_admin']) {
    echo json_encode(['success' => false, 'error' => 'Acesso negado.']);
    exit;
}

// 3. Receber e Decodificar o JSON
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos.']);
    exit;
}

// 4. Validação Básica
$camposObrigatorios = ['titulo', 'disciplina', 'data', 'turma_id'];
foreach ($camposObrigatorios as $campo) {
    if (empty($data[$campo])) {
        echo json_encode(['success' => false, 'error' => "O campo $campo é obrigatório."]);
        exit;
    }
}

try {
    // 5. Criação do Bean (Objeto)
    // 'atividade' será o nome da tabela no banco
    $atividade = R::dispense('atividade');

    // 6. Preenchendo os dados
    $atividade->titulo         = $data['titulo'];
    $atividade->tipo           = $data['tipo'];
    $atividade->disciplina     = $data['disciplina'];
    $atividade->data_atividade = $data['data'];
    $atividade->descricao      = $data['descricao'] ?? null;
    $atividade->turma          = $_SESSION['turma_atual']['id'];
    $atividade->criado_por     = $_SESSION['usuario']['id'];
    
    // Tratamento de Data/Hora (Concatenação)
    // O RedBean lida bem com strings no formato ISO (YYYY-MM-DD HH:MM:SS)
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

    // TIMESTAMP automático (Opcional, pois RedBean não cria created_at por padrão automaticamente como o Laravel)
    $atividade->created_at = R::isoDateTime();

    // 7. Persistência (Salvar)
    // R::store retorna o ID do registro criado
    $id = R::store($atividade);

    // 8. Retorno Sucesso
    echo json_encode([
        'success' => true, 
        'message' => 'Atividade salva com sucesso!',
        'id' => $id
    ]);

} catch (Exception $e) {
    // Log do erro para o servidor
    error_log($e->getMessage());

    echo json_encode([
        'success' => false, 
        'error' => 'Erro ao salvar atividade. Tente novamente.'
    ]);
}

// Opcional: Fechar conexão (RedBean fecha sozinho no fim do script, mas se quiser forçar:)
// R::close();