<?php
include 'conexao.php'; // Arquivo com conexão ao banco

$nome = trim($_POST['inputNome']);
$email = trim($_POST['inputEmail']);
$senha = $_POST['inputSenha'];
$confirma = $_POST['inputConfirmaSenha'];

if ($senha !== $confirma) {
    echo "<script>alert('As senhas não coincidem.'); window.location='login.php';</script>";
    exit;
}

// Verifica se o email já está cadastrado
$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conec->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Email já cadastrado.'); window.location='login.php';</script>";
    exit;
}

// Criptografa a senha
$senhaSegura = password_hash($senha, PASSWORD_DEFAULT);

// Insere o usuário
$sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
$stmt = $conec->prepare($sql);
$stmt->bind_param("sss", $nome, $email, $senhaSegura);

if ($stmt->execute()) {
    echo "<script>alert('Cadastro realizado com sucesso!'); window.location='login.php';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar.'); window.location='login.php';</script>";
}
