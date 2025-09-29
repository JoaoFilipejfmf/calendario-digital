<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Educacional - Organize Estudos e Tarefas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="bg-gray-50 text-gray-800">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-10">
        <nav class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/3af0bbcb-761f-4381-87a8-6df675c9e2e9.png"
                    alt="Ícone de calendário educacional com livro aberto e datas organizadas em azul e verde"
                    class="w-10 h-10">
                <h1 class="text-2xl font-bold text-blue-600">EduCalendar</h1>
            </div>
            <ul class="hidden md:flex space-x-6">
                <li><a href="#features" class="text-gray-600 hover:text-blue-600 transition">Recursos</a></li>
                <li><a href="#team" class="text-gray-600 hover:text-blue-600 transition">Equipe</a></li>
                <li><a href="#contact" class="text-gray-600 hover:text-blue-600 transition">Contato</a></li>
            </ul>
            <button class="md:hidden" onclick="toggleMenu()">☰</button>
        </nav>
        <div id="mobile-menu" class="md:hidden hidden bg-white px-4 py-2">
            <ul class="space-y-2">
                <li><a href="#features" class="block text-gray-600 hover:text-blue-600">Recursos</a></li>
                <li><a href="#team" class="block text-gray-600 hover:text-blue-600">Equipe</a></li>
                <li><a href="#contact" class="block text-gray-600 hover:text-blue-600">Contato</a></li>
            </ul>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-bg text-center py-20">
        <div class="max-w-4xl mx-auto px-4">
            <h2 class="text-4xl md:text-6xl font-extrabold text-blue-600 mb-4">Organize Estudos e Tarefas com Eficiência
            </h2>
            <p class="text-xl md:text-2xl text-gray-700 mb-8">Um calendário educacional que une estudantes e professores
                em um ambiente colaborativo, prevenindo sobrecargas e promovendo aprendizado equilibrado.</p>
           <a href="login.html" class="bg-blue-600 text-white px-8 py-4 rounded-full text-lg hover:bg-blue-700 transition inline-block text-center">Comece Agora</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12">Recursos Exclusivos para Educação</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: Alunos -->
                <div class="bg-gray-100 p-6 rounded-lg shadow-lg task-card">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/77db0062-db54-4822-b8e1-c2f20d61c58b.png"
                        alt="Estudante jovem marcando compromissos em um calendário digital com ícones de livros e relógio"
                        class="w-full h-48 object-cover mb-4">
                    <h4 class="text-2xl font-semibold mb-2">Para Estudantes</h4>
                    <p>Marque compromissos facilmente, receba lembretes e evite esquecimentos. Navegue por um sistema
                        intuitivo que mantém você no controle de seus estudos.</p>
                </div>
                <!-- Feature 2: Professores -->
                <div class="bg-gray-100 p-6 rounded-lg shadow-lg task-card">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/537dd8fb-92da-4413-b087-e7aa11018b99.png"
                        alt="Professor configurando limites de tarefas semanais com gráficos de progresso e ícones educacionais"
                        class="w-full h-48 object-cover mb-4">
                    <h4 class="text-2xl font-semibold mb-2">Para Professores</h4>
                    <p>Defina limites diários ou semanais de tarefas para prevenir sobrecargas. Garanta um equilíbrio
                        saudável no aprendizado dos alunos.</p>
                    <div class="mt-4">
                        <p class="text-sm">Limite Semanal:<br>
                        <div class="limit-progress">
                            <div class="limit-fill" style="width: 70%;"></div>
                        </div>
                        <span>70% Utilizado</span></p>
                    </div>
                </div>
                <!-- Feature 3: Coletividade -->
                <div class="bg-gray-100 p-6 rounded-lg shadow-lg task-card">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/2ce6bcb4-1155-49d7-a7ee-c67655a8c3ce.png"
                        alt="Grupo de estudantes e professores colaborando online em um calendário compartilhado com chat integrado"
                        class="w-full h-48 object-cover mb-4">
                    <h4 class="text-2xl font-semibold mb-2">Facilidade e Coletividade</h4>
                    <p>Ambiente online integrado para comunicação em tempo real. Todos participam na construção do
                        calendário, promovendo transparência e colaboração educacional.</p>
                </div>
            </div>
            <!-- Additional Example Calendar -->
            <div class="mt-12 text-center">
                <h4 class="text-2xl font-semibold mb-4">Exemplo de Detalhes de Atividades</h4>
                <div class="calendar-grid bg-white border rounded-lg p-4 max-w-2xl mx-auto">
                    <div class="bg-blue-200 p-4 rounded">Seg - Matemática: Revisão de álgebra (Material: Livro p.50)
                    </div>
                    <div class="bg-green-200 p-4 rounded">Ter - História: Leitura sobre Revolução Industrial (Vídeo
                        didático anexado)</div>
                    <div class="bg-yellow-200 p-4 rounded">Qua -Ciência: Experimento com plantas (Materiais: Sementes e
                        potes)</div>
                    <div class="bg-purple-200 p-4 rounded">Qui - Língua Portuguesa: Redação (Tópico: Meio Ambiente)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team/Product Promotion Section -->
    <section id="team" class="py-16 bg-blue-50">
        <div class="max-w-7xl mx-auto px-4">
            <h3 class="text-3xl font-bold text-center mb-12">Conheça Nossa Equipe e Produto</h3>
            <p class="text-center text-lg mb-8">Desenvolvido por educadores apaixonados para revolucionar a gestão
                educacional. O EduCalendar é irresistível pela sua simplicidade, impacto positivo na saúde mental dos
                alunos e foco no sucesso coletivo.</p>
            <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-8">
                <div class="text-center">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/6970a341-4208-4eff-81cd-275972c011bc.png"
                        alt="Foto profissional de Maria Silva, educadora com experiência em pedagogia inovadora, sorrindo em um ambiente escolar"
                        class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h4 class="text-xl font-semibold">João Filipe</h4>
                    <p>Estudante do curso Técnico em Informática e Principal desenvolvedor</p>
                </div>
                <div class="text-center">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/4a61631d-7953-42f1-a25b-6c7eb8667bb2.png"
                        alt="Foto profissional de João Pereira, desenvolvedor de software educacional, segurando um laptop em um escritório moderno"
                        class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h4 class="text-xl font-semibold">Pedro Lucas</h4>
                    <p>Estudante do curso Técnico em Informática</p>
                </div>
                <div class="text-center">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/c689d857-e48e-44ff-8566-894a6d8fa689.png"
                        alt="Foto profissional de Ana Costa, designer UX com foco em interfaces intuitivas para educação, trabalhando em um tablet"
                        class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h4 class="text-xl font-semibold">Luiz Eduardo</h4>
                    <p>Estudante do curso Técnico em Informática</p>
                </div>
                <!-- Quarto Membro da Equipe -->
                <div class="text-center">
                    <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/e2ef0262-7017-4961-bbfb-6dcb4ae73a0d.png"
                        alt="Foto profissional de Carlos Mendes, psicólogo educacional especializado em bem-estar emocional de estudantes, em consultório com livros e plantas"
                        class="w-32 h-32 rounded-full mx-auto mb-4">
                    <h4 class="text-xl font-semibold">João Víctor</h4>
                    <p>Estudante do curso Técnico em Informática</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-blue-600 text-white text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h3 class="text-3xl font-bold mb-4">Junte-se à Revolução Educacional</h3>
            <p class="text-xl mb-8">Registre-se gratuitamente e transforme a gestão de estudos e tarefas.</p>
            <a href="login.html" class="bg-white text-blue-600 px-8 py-4 rounded-full text-lg hover:bg-gray-100 transition inline-block text-center">Registrar Agora</a>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p>© 2025 EduCalendar. Todos os direitos reservados.</p>
            <p>Contato: info@educalendar.com | Suporte: 24/7</p>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
        // Simulação de interação com limite de tarefas
        const fillBar = document.querySelector('.limit-fill');
        let width = 70;
        setInterval(() => {
            width = (width >= 100) ? 110 : width++;
            fillBar.style.width = width + '%';
        }, 10000); // Aumenta lentamente para demonstrar dinamismo
    </script>
</body>

</html>
</content>
</create_file>