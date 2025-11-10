<?php
require 'conexao.php'; // Conecta via RedBeanPHP

$nome = trim($_POST['inputNome']);
$email = trim($_POST['inputEmail']);
$senha = $_POST['inputSenha'];
$confirma = $_POST['inputConfirmaSenha'];

// 1️⃣ — Verifica se as senhas coincidem
if ($senha !== $confirma) {
    echo "<script>alert('As senhas não coincidem.'); window.location='login.php';</script>";
    exit;
}

// 2️⃣ — Verifica se o email já está cadastrado
$usuarioExistente = R::findOne('usuarios', ' email = ? ', [$email]);

if ($usuarioExistente) {
    echo "<script>alert('Email já cadastrado.'); window.location='login.php';</script>";
    exit;
}

// 3️⃣ — Criptografa a senha
$senhaSegura = password_hash($senha, PASSWORD_DEFAULT);

// 4️⃣ — Cria e armazena o novo usuário
$usuario = R::dispense('usuarios'); // Cria um "bean" da tabela 'usuarios'
$usuario->nome = $nome;
$usuario->email = $email;
$usuario->senha = $senhaSegura;

R::store($usuario); // Salva o registro no banco

// 5️⃣ — Mensagem de sucesso
echo "<script>alert('Cadastro realizado com sucesso!'); window.location='login.php';</script>";
?>
