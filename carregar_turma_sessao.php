<?php

$sql = "
    SELECT 
        turma.*, 
        participacaoturma.administrador AS is_admin 
    FROM 
        turma
    INNER JOIN 
        participacaoturma ON participacaoturma.turma_id = turma.id 
        AND participacaoturma.usuario_id = ?
    ORDER BY
        turma.nome ASC
";

$parametros = [$usuario->id];

// Executa a consulta
$turmas_usuario = R::getAll($sql, $parametros);

// $turmas agora é um array de arrays associativos do PHP

if (count($turmas_usuario) > 0) {
    if (!isset($_SESSION['turma_atual'])) {
        $_SESSION['turma_atual'] = $turmas_usuario[0];
    }
    $turma = $_SESSION['turma_atual'];
    $isAdmin = $turma['is_admin'];
}

$possui_turma = isset($_SESSION['turma_atual']);