<?php
session_start();
require 'conexao.php'; // Arquivo que faz o R::setup()

// Recebe os dados do formulário
$email = trim($_POST['email']);
$senha = $_POST['senha'];  // Senha digitada

// Busca o usuário pelo e-mail
$usuario = R::findOne('usuario', ' email = ? ', [$email]);

// Verifica se o usuário existe e se a senha digitada confere com a senha no banco
if ($usuario && password_verify($senha, $usuario->senha)) {
    // Login bem-sucedido
    $_SESSION['usuario'] = $usuario;
    header("Location: main.php"); // Redireciona após login
    exit;
} else {
    // Falha no login
    echo "<script>alert('Email ou senha incorretos.'); window.location='login.php';</script>";
}
?>
