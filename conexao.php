<?php
$host = "localhost";
$port = 3306;
$user = "root";
$password = "aluno";
$dbname = "eduCalendar";

// Conecta apenas ao servidor (sem banco)
$conec = mysqli_connect($host, $user, $password, "", $port);

if (!$conec) {
    die("Falha na conexão com o servidor MySQL: " . mysqli_connect_error());
}

// Cria banco se não existir
$sql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
if (mysqli_query($conec, $sql)) {
      $criou = 1;
} else {
    die("Erro ao criar banco: " . mysqli_error($conec));
}

// Conecta ao banco agora já criado
$conec = mysqli_connect($host, $user, $password, $dbname, $port);

if (!$conec) {
    die("Falha na conexão com o banco: " . mysqli_connect_error());
} else {
      $conectou = 1;
}
?>
