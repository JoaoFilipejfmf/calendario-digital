<?php
session_start();

$nome = $_POST['registerName'];
$email = $_POST['registerEmail'];
$senha = $_POST['registerPassword'];

include 'conexao.php';

if ($conectou) {

    // Verifica se o usuário já existe
    $sql = "SELECT * FROM usuarios WHERE email = '$email'";
    $buscar = mysqli_query($conec, $sql);

    if (mysqli_num_rows($buscar) != 0) {
        echo "<script>alert('Usuário já cadastrado!');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=login.php'>";
        exit;
    } else {
        // Criptografando a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senhaHash')";
        $inserir = mysqli_query($conec, $sql);

        if ($inserir) {
            echo "<script>alert('Usuário cadastrado com sucesso!');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=login.php'>";
        } else {
            echo "<script>alert('Erro ao cadastrar usuário!');</script>";
            echo "<meta http-equiv='refresh' content='0;URL=login.php'>";
        }
    }
}
