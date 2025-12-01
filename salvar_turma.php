<?php
// 1. Configurações Iniciais e Dependências
require_once "conexao.php";
header('Content-Type: application/json');

session_start();
// 2. Verificações de Segurança
if (!isset($_SESSION['usuario'])) {
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
$camposObrigatorios = ['nome'];
foreach ($camposObrigatorios as $campo) {
    if (empty($data[$campo])) {
        echo json_encode(['success' => false, 'error' => "O campo $campo é obrigatório."]);
        exit;
    }
}

function generateTurmaCode(int $length = 9): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    $max = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $characters[random_int(0, $max)];
    }

    return $code;
}

function generateUniqueTurmaCode(int $length = 9): string
{
    do {
        // 1. Gera um novo código
        $code = generateTurmaCode($length);

        // 2. Verifica se o código já existe no DB
        $exists = R::findOne('turma', 'codigo = ?', [$code]);
    } while ($exists); // Repete enquanto o código for encontrado (não for NULL)

    return $code;
}

try {
    // 5. Criação do Bean (Objeto)
    // 'atividade' será o nome da tabela no banco
    $turma = R::dispense('turma');

    // 6. Preenchendo os dados
    $turma->nome = $data['nome'];
    $turma->codigo = generateUniqueTurmaCode(9);
    $turma->criador = $_SESSION['usuario']['id'];

    // 7. Persistência (Salvar)
    // R::store retorna o ID do registro criado

    $participacaoturma = R::dispense('participacaoturma');

    $id = R::store($turma);

    $participacaoturma->usuario_id = $_SESSION['usuario']->id;
    $participacaoturma->turma_id = $id;
    $participacaoturma->administrador = true;

    R::store($participacaoturma);

    $_SESSION['turma_atual'] = $turma;
    $_SESSION['turma_atual']['id'] = $id;
    $isAdmin = true;
    $turma = $_SESSION['turma_atual'];
    $possui_turma = isset($_SESSION['turma_atual']);

    // 8. Retorno Sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Turma criada com sucesso!',
        'id' => $id
    ]);
} catch (Exception $e) {
    // Log do erro para o servidor
    error_log($e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => 'Erro ao criar turma. Tente novamente.'
    ]);
}

// Opcional: Fechar conexão (RedBean fecha sozinho no fim do script, mas se quiser forçar:)
// R::close();