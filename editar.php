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
$usuario = R::load('usuario', $usuario_id);

// Verifica se o usuário foi encontrado
if (!$usuario) {
    echo "Usuário não encontrado!";
    exit;
}

// Verifica se o formulário de edição foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Atualiza o nome de usuário se foi enviado
    if (isset($_POST['nome']) && !empty(trim($_POST['nome']))) {
        $novo_nome = trim($_POST['nome']);
        $usuario->nome = $novo_nome;
        $mensagem_sucesso = "Nome atualizado com sucesso!";
    }

    // Atualiza a senha se foi enviada
    if (isset($_POST['nova_senha']) && !empty($_POST['nova_senha'])) {
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        if ($nova_senha !== $confirmar_senha) {
            $mensagem_erro = "As senhas não coincidem!";
        } else {
            // Criptografa a nova senha
            $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);
            $usuario->senha = $senha_criptografada;
            $mensagem_sucesso = isset($mensagem_sucesso) ? 
                "Nome e senha atualizados com sucesso!" : 
                "Senha atualizada com sucesso!";
        }
    }

    // Salva as alterações no banco de dados se não houve erro
    if (!isset($mensagem_erro)) {
        R::store($usuario);
        if (isset($mensagem_sucesso)) {
            echo "<script>alert('$mensagem_sucesso'); window.location = 'perfil.php';</script>";
        }
    } else {
        echo "<script>alert('$mensagem_erro');</script>";
    }
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
    <div class="container mx-auto p-6 max-w-2xl">
        <h1 class="text-3xl font-bold text-blue-600 text-center mb-6">Editar Perfil</h1>

        <!-- Card para alterar nome de usuário -->
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-user-edit text-blue-500 mr-2"></i>
                Alterar Nome de Usuário
            </h2>

            <form action="editar.php" method="post">
                <div class="mb-4">
                    <label for="nome" class="block text-gray-700 mb-2">Nome de Usuário</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($usuario->nome); ?>" 
                           class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Digite seu nome de usuário">
                </div>

                <button type="submit" name="alterar_nome" 
                        class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-2"></i>Atualizar Nome
                </button>
            </form>
        </div>

        <!-- Card para alterar senha -->
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-lock text-blue-500 mr-2"></i>
                Alterar Senha
            </h2>

            <form action="editar.php" method="post">
                <div class="mb-4">
                    <label for="nova_senha" class="block text-gray-700 mb-2">Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" 
                           class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Digite a nova senha">
                </div>

                <div class="mb-4">
                    <label for="confirmar_senha" class="block text-gray-700 mb-2">Confirmar Nova Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" 
                           class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Confirme a nova senha">
                </div>

                <button type="submit" name="alterar_senha" 
                        class="w-full bg-green-600 text-white py-3 rounded hover:bg-green-700 transition font-medium">
                    <i class="fas fa-key mr-2"></i>Atualizar Senha
                </button>
            </form>
        </div>

        <a href="atividades.php" class="mt-6 inline-block text-blue-600 hover:underline flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Calendário
        </a>
    </div>

    <!-- Adicione Font Awesome para os ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>