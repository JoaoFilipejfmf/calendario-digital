<?php
require 'conexao.php'; // Arquivo que faz o R::setup()

session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    // Caso não esteja logado, redireciona para o login
    header("Location: login.php");
    exit;
}

// Carrega as informações do usuário
$usuario = $_SESSION['usuario'];

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - EduCalendar</title>
    <link href="css/styles.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .fc-event {
            cursor: pointer;
        }
        .fc-daygrid-day-number {
            color: #333;
        }
        .fc-toolbar-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .subject-color-1 { background-color: #3b82f6; }
        .subject-color-2 { background-color: #ef4444; }
        .subject-color-3 { background-color: #10b981; }
        .subject-color-4 { background-color: #f59e0b; }
        .subject-color-5 { background-color: #8b5cf6; }
        .subject-color-6 { background-color: #ec4899; }
    </style>
</head>
<body>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-blue-600 text-center mb-6">Perfil de <?php echo htmlspecialchars($usuario->nome); ?></h1>
        
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Informações do Usuário</h2>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuario->nome); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($usuario->email); ?></p>

            <!-- Link para editar -->
            <a href="editar.php" class="text-blue-600 hover:underline mt-4 inline-block">Editar</a>
        </div>

        <a href="logout.php" class="mt-4 inline-block text-blue-600 hover:underline">Sair</a>
    </div>
</body>
</html>
