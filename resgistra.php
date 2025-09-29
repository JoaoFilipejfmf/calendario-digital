<?php
session_start();

$email = $_POST['loginEmail'];
$senha = $_POST['loginPassword'];

include 'conexao.php';

if ($conectou) {
    
    $sql = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $buscar = mysqli_query($conec, $sql);

    if (mysqli_num_rows($buscar) > 0) {
        $dados = mysqli_fetch_array($buscar);

        $_SESSION['email'] = $dados['email'];
        //$_SESSION['permissao'] = $dados['permissao'] ?? "comum"; // se não tiver no BD, assume "comum"

        header("Location: teste.php");
        exit;
    } else {
        echo "<script>alert('Erro nos dados do Usuário/Senha!');</script>";
        echo "<meta http-equiv='refresh' content='0;URL=index.php'>";
    }
}

?>
