<?php
session_start();
require 'conexao.php'; // Arquivo que faz o R::setup()

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Carrega as informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$usuario = R::load('usuarios', $usuario_id);

// Verifica se o usuário foi encontrado
if (!$usuario) {
    echo "Usuário não encontrado!";
    exit;
}

// Verifica se o formulário de edição foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe os dados do formulário
    $novo_nome = trim($_POST['nome']);
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Valida se as senhas são iguais
    if (!empty($nova_senha) || !empty($confirmar_senha)) {
        if ($nova_senha !== $confirmar_senha) {
            echo "<script>alert('As senhas não coincidem!');</script>";
        } else {
            // Criptografa a nova senha
            $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);
            $usuario->senha = $senha_criptografada;
        }
    }

    // Atualiza o nome de usuário
    if (!empty($novo_nome)) {
        $usuario->nome = $novo_nome;
    }

    // Salva as alterações no banco de dados
    R::store($usuario);

    // Mensagem de sucesso
    echo "<script>alert('Dados atualizados com sucesso!'); window.location = 'perfil.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - EduCalendar</title>
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-600 text-center mb-6">Editar Perfil</h1>

        <!-- Formulário de edição de dados -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Editar Informações</h2>

            <form action="editar.php" method="post">
                <!-- Campo de Nome -->
                <div class="mb-4">
                    <label for="nome" class="block text-gray-700">Nome de Usuário</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario->nome); ?>" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Digite seu nome de usuário">
                </div>

                <!-- Campo de Nova Senha -->
                <div class="mb-4">
                    <label for="nova_senha" class="block text-gray-700">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Digite a nova senha">
                </div>

                <!-- Campo de Confirmar Senha -->
                <div class="mb-4">
                    <label for="confirmar_senha" class="block text-gray-700">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="w-full border border-gray-300 rounded px-4 py-2" placeholder="Confirme a nova senha">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition">Salvar Alterações</button>
            </form>
        </div>

        <a href="perfil.php" class="mt-4 inline-block text-blue-600 hover:underline">Voltar ao Perfil</a>
    </div>
</body>
</html>
