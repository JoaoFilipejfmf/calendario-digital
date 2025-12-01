<?php
require_once "conexao.php";

session_start();
if (isset($_SESSION['usuario'])) {
    header('location: main.php');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login / Cadastro - EduCalendar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg max-w-md w-full p-8">
        <h1 class="text-3xl font-bold text-blue-600 text-center mb-6">EduCalendar</h1>
        
        <!-- Abas -->
        <div class="flex justify-center mb-6">
            <button id="loginTab" class="px-6 py-2 font-semibold border-b-4 border-blue-600 text-blue-600 focus:outline-none">Login</button>
            <button id="registerTab" class="px-6 py-2 font-semibold text-gray-500 hover:text-blue-600 focus:outline-none">Cadastro</button>
        </div>

        <!-- Login -->
        <form id="loginForm" action="valida.php" method="post" class="space-y-6">
            <div>
                <label for="loginEmail" class="block text-gray-700 mb-1">Email</label>
                <input type="email" id="loginEmail" name="email" required placeholder="seu@email.com" 
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="loginPassword" class="block text-gray-700 mb-1">Senha</label>
                <input type="password" id="loginPassword" name="senha" required placeholder="••••••••" 
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition">Entrar</button>
        </form>

        <!-- Cadastro -->
        <form id="registerForm" action="cadastro.php" method="post" class="space-y-6 hidden">
            <div>
                <label for="inputNome" class="block text-gray-700 mb-1">Nome Completo</label>
                <input type="text" id="inputNome" name="inputNome" required placeholder="Seu nome" 
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="inputEmail" class="block text-gray-700 mb-1">Email</label>
                <input type="email" id="inputEmail" name="inputEmail" required placeholder="seu@email.com" 
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="registerPassword" class="block text-gray-700 mb-1">Senha</label>
                <input type="password" id="registerPassword" name="inputSenha" required placeholder="••••••••" 
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="registerConfirmPassword" class="block text-gray-700 mb-1">Confirmar Senha</label>
                <input type="password" id="registerConfirmPassword" name="inputConfirmaSenha" required placeholder="••••••••" 
                    class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition">Cadastrar</button>
        </form>
    </div>

    <script>
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

        // Alternar abas
        loginTab.addEventListener('click', () => {
            loginTab.classList.add('border-b-4', 'border-blue-600', 'text-blue-600');
            registerTab.classList.remove('border-b-4', 'border-blue-600', 'text-blue-600');
            registerTab.classList.add('text-gray-500');
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
        });

        registerTab.addEventListener('click', () => {
            registerTab.classList.add('border-b-4', 'border-blue-600', 'text-blue-600');
            loginTab.classList.remove('border-b-4', 'border-blue-600', 'text-blue-600');
            loginTab.classList.add('text-gray-500');
            registerForm.classList.remove('hidden');
            loginForm.classList.add('hidden');
        });

        // Validação de senhas
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const senha = document.getElementById('registerPassword').value;
            const confirma = document.getElementById('registerConfirmPassword').value;
            if (senha !== confirma) {
                e.preventDefault();
                alert('As senhas não coincidem.');
            }
        });
    </script>
 
    
</body>
</html>
