<?php
session_start();
require 'conexao.php'; // Arquivo que faz o R::setup()

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    // Caso não esteja logado, redireciona para o login
    header("Location: login.php");
    exit;
}

// Carrega as informações do usuário
$usuario_id = $_SESSION['usuario_id'];
$usuario = R::load('usuario', $usuario_id);

// Verifica se o usuário foi encontrado
if (!$usuario) {
    echo "Usuário não encontrado!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - EduCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Meu Perfil</h1>
            <p class="text-gray-600">Gerencie suas informações pessoais</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Esquerda - Card do Usuário -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 text-center">
                    <!-- Avatar -->
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-3xl"></i>
                    </div>
                    
                    <!-- Informações do Usuário -->
                    <h2 class="text-xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($usuario->nome); ?></h2>
                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($usuario->email); ?></p>
                    
                    <!-- Status -->
                    <div class="bg-green-50 border border-green-200 rounded-lg px-3 py-2 inline-block mb-6">
                        <span class="text-green-700 text-sm font-medium">
                            <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                            Conta ativa
                        </span>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="space-y-3">
                        <a href="editar.php" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Editar Perfil
                        </a>
                        
                        <a href="atividades.php" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 py-3 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Voltar ao Calendário
                        </a>
                    </div>
                </div>
            </div>

            <!-- Coluna Direita - Informações -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card de Informações Detalhadas -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Informações da Conta
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <i class="fas fa-user text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600">Nome completo</span>
                            </div>
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($usuario->nome); ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600">Email</span>
                            </div>
                            <span class="font-medium text-gray-800"><?php echo htmlspecialchars($usuario->email); ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-plus text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600">Membro desde</span>
                            </div>
                            <span class="font-medium text-gray-800"><?php echo date('d/m/Y'); ?></span>
                        </div>
                        
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center">
                                <i class="fas fa-shield-alt text-gray-400 mr-3 w-5"></i>
                                <span class="text-gray-600">Tipo de conta</span>
                            </div>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Padrão</span>
                        </div>
                    </div>
                </div>

                <!-- Card de Ações Rápidas -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Ações Rápidas
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <a href="editar.php" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-blue-50 hover:border-blue-200 transition duration-200">
                            <i class="fas fa-user-edit text-blue-500 mr-3"></i>
                            <span class="text-gray-700">Alterar dados</span>
                        </a>
                        
                        <a href="#" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-green-50 hover:border-green-200 transition duration-200">
                            <i class="fas fa-bell text-green-500 mr-3"></i>
                            <span class="text-gray-700">Notificações</span>
                        </a>
                        
                        <a href="logout.php" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-red-50 hover:border-red-200 transition duration-200">
                            <i class="fas fa-sign-out-alt text-red-500 mr-3"></i>
                            <span class="text-gray-700">Sair</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-12 text-center text-gray-500 text-sm pb-6">
        <p>EduCalendar &copy; <?php echo date('Y'); ?> - Todos os direitos reservados</p>
    </footer>
</body>
</html>