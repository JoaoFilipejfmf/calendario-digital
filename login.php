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
        <div class="flex justify-center mb-6">
            <button id="loginTab" class="px-6 py-2 font-semibold border-b-4 border-blue-600 text-blue-600 focus:outline-none">Login</button>
            <button id="registerTab" class="px-6 py-2 font-semibold text-gray-500 hover:text-blue-600 focus:outline-none">Cadastro</button>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label for="loginEmail" class="block text-gray-700 mb-1">Email</label>
                <input type="email" id="loginEmail" required placeholder="seu@email.com" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="loginPassword" class="block text-gray-700 mb-1">Senha</label>
                <input type="password" id="loginPassword" required placeholder="••••••••" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition">Entrar</button>
        </form>

        <form id="registerForm" class="space-y-6 hidden">
            <div>
                <label for="registerName" class="block text-gray-700 mb-1">Nome Completo</label>
                <input type="text" id="registerName" required placeholder="Seu nome" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="registerEmail" class="block text-gray-700 mb-1">Email</label>
                <input type="email" id="registerEmail" required placeholder="seu@email.com" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="registerPassword" class="block text-gray-700 mb-1">Senha</label>
                <input type="password" id="registerPassword" required placeholder="••••••••" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
                <label for="registerConfirmPassword" class="block text-gray-700 mb-1">Confirmar Senha</label>
                <input type="password" id="registerConfirmPassword" required placeholder="••••••••" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded hover:bg-blue-700 transition">Cadastrar</button>
        </form>
    </div>

    <script>
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');

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

        // Apenas para simular o envio dos formulários
        loginForm.addEventListener('submit', e => {
            e.preventDefault();
            alert('Login simulado! Email: ' + document.getElementById('loginEmail').value);
        });

        registerForm.addEventListener('submit', e => {
            e.preventDefault();
            alert('Cadastro simulado! Nome: ' + document.getElementById('registerName').value);
        });
    </script>
</body>
</html>
