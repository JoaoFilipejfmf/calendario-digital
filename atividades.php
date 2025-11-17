<?php
require_once 'conexao.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Buscar informações do usuário e sua turma
$usuario_id = $_SESSION['usuario_id'];
$usuario = R::getRow("SELECT * FROM usuario WHERE id = ?", [$usuario_id]);

// Buscar turma atual do usuário
$turma_usuario = R::getRow("
    SELECT t.* 
    FROM turma t 
    INNER JOIN usuario_participa_de_turma upt ON t.id = upt.turma_idturma 
    WHERE upt.usuario_idusuario = ? 
    LIMIT 1
", [$usuario_id]);

$turma_id = $turma_usuario ? $turma_usuario['id'] : null;

// Buscar todas as turmas do usuário
$turmas_usuario = R::getAll("
    SELECT t.*, upt.administrador 
    FROM turma t 
    INNER JOIN usuario_participa_de_turma upt ON t.id = upt.turma_idturma 
    WHERE upt.usuario_idusuario = ?
", [$usuario_id]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Escolar Digital</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .fc-event { cursor: pointer; }
        .fc-daygrid-day-number { color: #333; }
        .fc-toolbar-title { font-size: 1.5rem; font-weight: 600; }
        .tab-active { 
            background-color: #3b82f6; 
            color: white;
        }
        .tab-inactive { 
            background-color: #f3f4f6; 
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-2xl mr-3"></i>
                <h1 class="text-xl font-bold">Calendário Escolar Digital</h1>
                <?php if ($turma_usuario): ?>
                <span class="ml-4 text-sm bg-blue-500 px-2 py-1 rounded">Turma: <?= htmlspecialchars($turma_usuario['nome']) ?></span>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-4">
               <span class="hidden md:inline">Olá, <?= isset($usuario['nome']) ? htmlspecialchars($usuario['nome']) : 'Usuário' ?></span>
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center focus:outline-none">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <i class="fas fa-chevron-down ml-1 text-sm"></i>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                        <a href="perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                        <a href="#" id="turmasMenuBtn" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Turma(s)</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Calendar -->
            <div class="lg:w-2/3 bg-white rounded-lg shadow-md p-4">
                <div id="calendar"></div>
            </div>

            <!-- Right Column - Day Details -->
            <div class="lg:w-1/3 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-xl font-bold mb-4">Detalhes do Dia</h2>
                
                <div class="mb-6">
                    <h3 id="selectedDate" class="text-lg font-semibold text-blue-600">Selecione uma data</h3>
                </div>
                
                <div id="activitiesList">
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-calendar-day text-3xl mb-2"></i>
                        <p>Selecione uma data para ver as atividades</p>
                    </div>
                </div>
                
                <?php if ($turma_id): ?>
                <div class="mt-6">
                    <button id="addActivityBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> Adicionar Atividade
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modal de Gerenciamento de Turmas -->
    <div id="turmasModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
            <!-- Header do Modal -->
            <div class="bg-blue-600 text-white p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Gerenciar Turmas</h2>
                    <button id="fecharTurmasModal" class="text-white hover:text-blue-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Navegação por Abas -->
            <div class="bg-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex space-x-2 overflow-x-auto">
                    <?php if ($turma_usuario): ?>
                    <button class="tab-btn flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-all duration-200 tab-active" data-tab="turma-atual">
                        <i class="fas fa-users mr-2"></i>Turma Atual
                    </button>
                    <?php endif; ?>
                    
                    <button class="tab-btn flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-all duration-200 <?= !$turma_usuario ? 'tab-active' : 'tab-inactive' ?>" data-tab="minhas-turmas">
                        <i class="fas fa-list mr-2"></i>Minhas Turmas
                    </button>
                    
                    <button class="tab-btn flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-all duration-200 tab-inactive" data-tab="entrar-turma">
                        <i class="fas fa-sign-in-alt mr-2"></i>Entrar em Turma
                    </button>
                    
                    <button class="tab-btn flex-shrink-0 px-4 py-2 rounded-lg font-medium transition-all duration-200 tab-inactive" data-tab="criar-turma">
                        <i class="fas fa-plus-circle mr-2"></i>Criar Turma
                    </button>
                </div>
            </div>

            <!-- Conteúdo das Abas -->
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <!-- ABA: Turma Atual -->
                <div id="tab-turma-atual" class="tab-content <?= $turma_usuario ? 'block' : 'hidden' ?>">
                    <?php if ($turma_usuario): ?>
                    <div class="space-y-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h3 class="text-xl font-bold text-blue-800"><?= htmlspecialchars($turma_usuario['nome']) ?></h3>
                                    <p class="text-blue-600">Código: <span class="font-mono bg-blue-100 px-2 py-1 rounded"><?= htmlspecialchars($turma_usuario['codigo'] ?? 'N/A') ?></span></p>
                                </div>
                                <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full font-medium">
                                    Turma Atual
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="text-center p-3 bg-white rounded-lg border">
                                    <div class="text-2xl font-bold text-blue-600" id="totalMembros">-</div>
                                    <div class="text-sm text-gray-600">Total de Membros</div>
                                </div>
                                <div class="text-center p-3 bg-white rounded-lg border">
                                    <div class="text-2xl font-bold text-green-600" id="totalAtividades">-</div>
                                    <div class="text-sm text-gray-600">Atividades</div>
                                </div>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button id="verMembrosBtn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium flex items-center justify-center">
                                    <i class="fas fa-user-friends mr-2"></i> Ver Membros
                                </button>
                                <button id="sairTurmaBtn" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-medium flex items-center justify-center">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Sair da Turma
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- ABA: Minhas Turmas -->
                <div id="tab-minhas-turmas" class="tab-content <?= !$turma_usuario ? 'block' : 'hidden' ?>">
                    <div class="space-y-4">
                        <?php if (count($turmas_usuario) > 0): ?>
                            <?php foreach ($turmas_usuario as $turma): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex justify-between items-center">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($turma['nome']) ?></h4>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="text-xs text-gray-500">Código: <?= htmlspecialchars($turma['codigo'] ?? 'N/A') ?></span>
                                            <?php if ($turma['id'] == $turma_id): ?>
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Atual</span>
                                            <?php endif; ?>
                                            <?php if ($turma['administrador'] == 1): ?>
                                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Administrador</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <?php if ($turma['id'] != $turma_id): ?>
                                        <button class="mudarTurmaBtn bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium" 
                                                data-turma-id="<?= $turma['id'] ?>">
                                            <i class="fas fa-check mr-1"></i> Selecionar
                                        </button>
                                        <?php endif; ?>
                                        <button class="sairTurmaIndividualBtn bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-medium" 
                                                data-turma-id="<?= $turma['id'] ?>" data-turma-nome="<?= htmlspecialchars($turma['nome']) ?>">
                                            <i class="fas fa-times mr-1"></i> Sair
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <i class="fas fa-users text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-500">Você não está em nenhuma turma</p>
                                <p class="text-gray-400 text-sm mt-1">Entre em uma turma existente ou crie uma nova</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- ABA: Entrar em Turma -->
                <div id="tab-entrar-turma" class="tab-content hidden">
                    <div class="max-w-md mx-auto">
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                            <div class="text-center mb-6">
                                <i class="fas fa-key text-purple-500 text-4xl mb-3"></i>
                                <h3 class="text-xl font-bold text-purple-800">Entrar em uma Turma</h3>
                                <p class="text-purple-600 text-sm mt-1">Digite o código de acesso fornecido pelo administrador</p>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="codigoTurma" class="block text-sm font-medium text-gray-700 mb-2">Código da Turma</label>
                                    <input type="text" id="codigoTurma" placeholder="Ex: TURMA123" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg text-center font-mono focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                </div>
                                
                                <button id="entrarTurmaBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-medium text-lg flex items-center justify-center">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Entrar na Turma
                                </button>
                            </div>
                            
                            <div class="mt-6 p-4 bg-purple-100 rounded-lg">
                                <p class="text-purple-700 text-sm flex items-start">
                                    <i class="fas fa-info-circle mr-2 mt-0.5"></i>
                                    <span>Peça o código de acesso para o administrador da turma que deseja entrar</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA: Criar Turma -->
                <div id="tab-criar-turma" class="tab-content hidden">
                    <div class="max-w-md mx-auto">
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                            <div class="text-center mb-6">
                                <i class="fas fa-plus-circle text-orange-500 text-4xl mb-3"></i>
                                <h3 class="text-xl font-bold text-orange-800">Criar Nova Turma</h3>
                                <p class="text-orange-600 text-sm mt-1">Crie uma nova turma e compartilhe o código com outros usuários</p>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="nomeNovaTurma" class="block text-sm font-medium text-gray-700 mb-2">Nome da Turma</label>
                                    <input type="text" id="nomeNovaTurma" placeholder="Ex: Matemática 2024 - Turma A" 
                                           class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                </div>
                                
                                <button id="criarTurmaBtn" class="w-full bg-orange-600 hover:bg-orange-700 text-white py-3 px-4 rounded-lg font-medium text-lg flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Criar Turma
                                </button>
                            </div>
                            
                            <div class="mt-6 p-4 bg-orange-100 rounded-lg">
                                <p class="text-orange-700 text-sm flex items-start">
                                    <i class="fas fa-lightbulb mr-2 mt-0.5"></i>
                                    <span>Após criar a turma, você receberá um código único para compartilhar com outros usuários</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer do Modal -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end">
                    <button id="fecharModalBtn" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-medium">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Lista de Membros -->
    <div id="membrosModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="bg-blue-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold">Membros da Turma</h3>
                    <button id="fecharMembrosModal" class="text-white hover:text-blue-200 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 max-h-96 overflow-y-auto">
                <div id="listaMembros">
                    <div class="text-center py-4">
                        <i class="fas fa-spinner fa-spin text-blue-500 text-2xl mb-2"></i>
                        <p class="text-gray-500">Carregando membros...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ========== CALENDÁRIO ==========
            const calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: 'buscar_atividades.php?turma_id=' + (<?= $turma_id ?: 'null' ?> || ''),
                    dateClick: function(info) {
                        const date = info.date;
                        const options = { day: 'numeric', month: 'long', year: 'numeric' };
                        document.getElementById('selectedDate').textContent = date.toLocaleDateString('pt-BR', options);
                        
                        if (<?= $turma_id ?: 'null' ?>) {
                            buscarAtividadesData(info.dateStr);
                        } else {
                            document.getElementById('activitiesList').innerHTML = `
                                <div class="text-center py-4 text-yellow-500">
                                    <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                                    <p>Você não está vinculado a nenhuma turma</p>
                                </div>
                            `;
                        }
                    },
                    eventClick: function(info) {
                        buscarDetalhesAtividade(info.event.id);
                    }
                });
                calendar.render();
            }

            // ========== MENU DO USUÁRIO ==========
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');

            if (userMenuButton && userMenu) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(e) {
                    if (!userMenu.contains(e.target) && !userMenuButton.contains(e.target)) {
                        userMenu.classList.add('hidden');
                    }
                });
            }

            // ========== SISTEMA DE ABAS ==========
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Atualizar botões
                    tabButtons.forEach(btn => {
                        btn.classList.remove('tab-active');
                        btn.classList.add('tab-inactive');
                    });
                    this.classList.remove('tab-inactive');
                    this.classList.add('tab-active');
                    
                    // Atualizar conteúdos
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.getElementById(`tab-${targetTab}`).classList.remove('hidden');
                    
                    // Carregar dados específicos da aba se necessário
                    if (targetTab === 'turma-atual' && <?= $turma_id ?: 'null' ?>) {
                        carregarEstatisticasTurma(<?= $turma_id ?>);
                    }
                });
            });

            // ========== MODAL DE TURMAS ==========
            const turmasModal = document.getElementById('turmasModal');
            const turmasMenuBtn = document.getElementById('turmasMenuBtn');
            const fecharTurmasModal = document.getElementById('fecharTurmasModal');
            const fecharModalBtn = document.getElementById('fecharModalBtn');

            // Abrir modal de turmas
            if (turmasMenuBtn) {
                turmasMenuBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    turmasModal.classList.remove('hidden');
                    if (userMenu) userMenu.classList.add('hidden');
                    
                    // Carregar estatísticas da turma atual se existir
                    if (<?= $turma_id ?: 'null' ?>) {
                        carregarEstatisticasTurma(<?= $turma_id ?>);
                    }
                });
            }

            // Fechar modal de turmas
            function fecharModalTurmas() {
                turmasModal.classList.add('hidden');
            }

            if (fecharTurmasModal) fecharTurmasModal.addEventListener('click', fecharModalTurmas);
            if (fecharModalBtn) fecharModalBtn.addEventListener('click', fecharModalTurmas);

            // Fechar modal ao clicar fora
            if (turmasModal) {
                turmasModal.addEventListener('click', function(e) {
                    if (e.target === turmasModal) {
                        fecharModalTurmas();
                    }
                });
            }

            // ========== FUNÇÕES DOS BOTÕES ==========
            
            // Mudar de turma
            document.querySelectorAll('.mudarTurmaBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const turmaId = this.getAttribute('data-turma-id');
                    if (confirm('Deseja mudar para esta turma?')) {
                        mudarTurmaAtual(turmaId);
                    }
                });
            });

            // Entrar em turma
            const entrarTurmaBtn = document.getElementById('entrarTurmaBtn');
            if (entrarTurmaBtn) {
                entrarTurmaBtn.addEventListener('click', function() {
                    const codigo = document.getElementById('codigoTurma').value.trim();
                    if (codigo) {
                        entrarNaTurma(codigo);
                    } else {
                        alert('Por favor, digite o código da turma');
                    }
                });
            }

            // Criar turma
            const criarTurmaBtn = document.getElementById('criarTurmaBtn');
            if (criarTurmaBtn) {
                criarTurmaBtn.addEventListener('click', function() {
                    const nomeTurma = document.getElementById('nomeNovaTurma').value.trim();
                    if (nomeTurma) {
                        criarTurma(nomeTurma);
                    } else {
                        alert('Por favor, digite o nome da turma');
                    }
                });
            }

            // Sair da turma atual
            const sairTurmaBtn = document.getElementById('sairTurmaBtn');
            if (sairTurmaBtn) {
                sairTurmaBtn.addEventListener('click', function() {
                    if (confirm('Tem certeza que deseja sair desta turma?')) {
                        sairDaTurma(<?= $turma_id ?>);
                    }
                });
            }

            // Sair de turma individual
            document.querySelectorAll('.sairTurmaIndividualBtn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const turmaId = this.getAttribute('data-turma-id');
                    const turmaNome = this.getAttribute('data-turma-nome');
                    if (confirm(`Tem certeza que deseja sair da turma "${turmaNome}"?`)) {
                        sairDaTurma(turmaId);
                    }
                });
            });

            // ========== FUNÇÕES AJAX ==========
            
            function carregarEstatisticasTurma(turmaId) {
                if (!turmaId) return;
                
                fetch(`buscar_estatisticas_turma.php?turma_id=${turmaId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('totalMembros').textContent = data.totalMembros || '0';
                            document.getElementById('totalAtividades').textContent = data.totalAtividades || '0';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar estatísticas:', error);
                    });
            }

            // ... (as outras funções AJAX permanecem as mesmas do código anterior)
            // mudarTurmaAtual, entrarNaTurma, criarTurma, carregarMembrosTurma, etc.

        });
    </script>
</body>
</html>