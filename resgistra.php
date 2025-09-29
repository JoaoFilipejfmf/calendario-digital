<?php
session_start();

$nome = $_POST['registerName'];
$email = $_POST['loginEmail'];
$senha = $_POST['loginPassword'];

include 'conexao.php';

if ($conectou) {
    
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND nome = '$nome'";
    $buscar = mysqli_query($conec, $sql);

    if (mysqli_num_rows($buscar) != 0) {

        echo "<script>alert('Usuário já cadastrado!');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=login.php'>";       
        //$_SESSION['permissao'] = $dados['permissao'] ?? "comum"; // se não tiver no BD, assume "comum"
        
        exit;
    } else {
        
        $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha');";

        $inserir = mysqli_query($conec, $sql);


        echo "<script>alert('Usuário cadastrado com sucesso!');</script>";

        header("Location: login.php");
    }
}

?>
