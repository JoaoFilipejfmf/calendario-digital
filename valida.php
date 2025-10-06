<?php
session_start();
include 'conexao.php'; // Arquivo com conexão ao banco

$email = $_POST['email'];
$senha = $_POST['senha'];

// Busca o usuário
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conec->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario'] = $usuario['nome'];
    header("Location: teste.php"); // Redireciona após login
    exit;
} else {
    echo "<script>alert('Email ou senha incorretos.'); window.location='login.php';</script>";
}
