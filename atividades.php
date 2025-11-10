<?php
require_once 'conexao.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Buscar informações do usuário e sua turma
$usuario = $_SESSION['usuario'];

// Buscar turma do usuário
$turma_usuario = R::getRow("
    SELECT t.* 
    FROM turma t 
    INNER JOIN usuario_participa_de_turma upt ON t.idturma = upt.turma_idturma 
    WHERE upt.usuario_idusuario = ? 
    LIMIT 1
", [$usuario->idusuario]);

$turma_id = $turma_usuario ? $turma_usuario['idturma'] : null;
$isProfessor = $usuario['tipo'] == 1; // Assumindo que tipo 1 = professor
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
                <span class="hidden md:inline">Olá, <?= htmlspecialchars($usuario['nome']) ?></span>
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center focus:outline-none">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <i class="fas fa-chevron-down ml-1 text-sm"></i>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="abrirModalTurmas()">Trocar de Turma</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="abrirModalEntrarTurma()">Entrar em Turma</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a>
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
                
                <?php if ($isProfessor && $turma_id): ?>
                <div class="mt-6">
                    <button id="addActivityBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> Adicionar Atividade
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Modais (mantenha os mesmos) -->
    <!-- Activity Details Modal -->
    <div id="activityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Detalhes da Atividade</h3>
                <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <div class="mb-4">
                    <h4 id="modalTitle" class="text-xl font-bold mb-2">Matemática - Prova Bimestral</h4>
                    <div class="flex items-center mb-2">
                        <span id="modalType" class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-2">Avaliação</span>
                        <span id="modalSubject" class="text-sm text-gray-600">Matemática</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5 class="font-semibold mb-1">Descrição:</h5>
                    <p id="modalDescription" class="text-gray-700">Conteúdo: Funções, equações e geometria analítica. A prova terá duração de 2 horas e será composta por questões objetivas e dissertativas.</p>
                </div>
                
                <div class="mb-4">
                    <h5 class="font-semibold mb-1">Data e Horário:</h5>
                    <p id="modalDateTime" class="text-gray-700">15 de Outubro de 2023, 08:00 - 10:00</p>
                </div>
                
                <div class="mb-4">
                    <h5 class="font-semibold mb-1">Professor:</h5>
                    <p id="modalTeacher" class="text-gray-700">Prof. Silva</p>
                </div>
            </div>
            <div class="flex justify-end p-4 border-t">
                <button id="editActivityBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md mr-2">
                    Editar
                </button>
                <button id="deleteActivityBtn" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-md">
                    Excluir
                </button>
            </div>
        </div>
    </div>

    <!-- Add Activity Modal -->
    <div id="addActivityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Adicionar Nova Atividade</h3>
                <button id="closeAddModalBtn" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="addActivityForm" class="p-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activityTitle">
                        Título da Atividade
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="activityTitle" type="text" placeholder="Ex: Prova de Matemática">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activityType">
                        Tipo de Atividade
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="activityType">
                        <option value="Avaliação">Avaliação</option>
                        <option value="Trabalho">Trabalho</option>
                        <option value="Atividade">Atividade</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activitySubject">
                        Disciplina
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                            id="activitySubject">
                        <option value="Matemática">Matemática</option>
                        <option value="Português">Português</option>
                        <option value="História">História</option>
                        <option value="Geografia">Geografia</option>
                        <option value="Biologia">Biologia</option>
                        <option value="Física">Física</option>
                        <option value="Química">Química</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activityDate">
                        Data
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                           id="activityDate" type="date">
                </div>
                
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="activityStartTime">
                            Horário de Início
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="activityStartTime" type="time">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="activityEndTime">
                            Horário de Término
                        </label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                               id="activityEndTime" type="time">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activityDescription">
                        Descrição
                    </label>
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                              id="activityDescription" rows="3" placeholder="Descreva os detalhes da atividade..."></textarea>
                </div>
            </form>
            <div class="flex justify-end p-4 border-t">
                <button id="cancelAddActivityBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md mr-2">
                    Cancelar
                </button>
                <button id="saveActivityBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                    Salvar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Trocar de Turma -->
    <div id="modalTurmas" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Trocar de Turma</h3>
                <button onclick="fecharModalTurmas()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4">
                <p class="text-gray-600 mb-4">Selecione a turma que deseja visualizar:</p>
                <div id="listaTurmas" class="max-h-60 overflow-y-auto">
                    <?php if (count($turmas_usuario) > 0): ?>
                        <?php foreach ($turmas_usuario as $turma): ?>
                        <div class="flex items-center justify-between p-3 border rounded-lg mb-2 hover:bg-gray-50 <?= $turma['idturma'] == $turma_id ? 'bg-blue-50 border-blue-200' : '' ?>">
                            <div>
                                <h4 class="font-semibold"><?= htmlspecialchars($turma['nome']) ?></h4>
                                <?php if ($turma['codigo']): ?>
                                <p class="text-sm text-gray-500">Código: <?= htmlspecialchars($turma['codigo']) ?></p>
                                <?php endif; ?>
                            </div>
                            <?php if ($turma['idturma'] == $turma_id): ?>
                            <span class="text-green-600 text-sm font-medium">Atual</span>
                            <?php else: ?>
                            <button onclick="trocarTurma(<?= $turma['idturma'] ?>)" class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-1 rounded">
                                Selecionar
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-users-slash text-3xl mb-2"></i>
                            <p>Você não está em nenhuma turma</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex justify-end p-4 border-t">
                <button onclick="fecharModalTurmas()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Entrar em Turma -->
    <div id="modalEntrarTurma" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-semibold">Entrar em Turma</h3>
                <button onclick="fecharModalEntrarTurma()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="formEntrarTurma" class="p-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="codigoTurma">
                        Código da Turma
                    </label>
                    <input 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" 
                        id="codigoTurma" 
                        type="text" 
                        placeholder="Digite o código da turma"
                        required
                    >
                    <p class="text-sm text-gray-500 mt-1">Peça o código para o professor da turma</p>
                </div>
                <div id="mensagemErro" class="hidden mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded"></div>
                <div id="mensagemSucesso" class="hidden mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded"></div>
            </form>
            <div class="flex justify-end p-4 border-t">
                <button onclick="fecharModalEntrarTurma()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md mr-2">
                    Cancelar
                </button>
                <button onclick="entrarTurma()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                    Entrar na Turma
                </button>
            </div>
        </div>
    </div>

    <script>
        // Variáveis globais para uso no JavaScript
        const usuarioTurmaId = <?= $turma_id ?: 'null' ?>;
        const isProfessor = <?= $isProfessor ? 'true' : 'false' ?>;

        document.addEventListener('DOMContentLoaded', function() {
            

            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: usuarioTurmaId ? 'buscar_atividades.php?turma_id=' + usuarioTurmaId : [],
                dateClick: function(info) {
                    const date = info.date;
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    document.getElementById('selectedDate').textContent = date.toLocaleDateString('pt-BR', options);
                    
                    if (usuarioTurmaId) {
                        buscarAtividadesData(info.dateStr);
                    } else {
                        document.getElementById('activitiesList').innerHTML = `
                            <div class="text-center py-4 text-yellow-500">
                                <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                                <p>Você não está vinculado a nenhuma turma</p>
                                <button onclick="abrirModalEntrarTurma()" class="mt-2 text-blue-600 hover:underline">
                                    Entrar em uma turma
                                </button>
                            </div>
                        `;
                    }
                },
                eventClick: function(info) {
                    buscarDetalhesAtividade(info.event.id);
                }
            });
            
            calendar.render();

            function buscarAtividadesData(data) {
                fetch(`buscar_atividades_data.php?data=${data}&turma_id=${usuarioTurmaId}`)
                    .then(response => response.json())
                    .then(atividades => {
                        renderizarAtividades(atividades);
                    })
                    .catch(error => {
                        console.error('Erro ao buscar atividades:', error);
                        document.getElementById('activitiesList').innerHTML = `
                            <div class="text-center py-4 text-red-500">
                                <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                                <p>Erro ao carregar atividades</p>
                            </div>
                        `;
                    });
            }

            function buscarDetalhesAtividade(idAtividade) {
                fetch(`buscar_detalhes_atividade.php?id=${idAtividade}`)
                    .then(response => response.json())
                    .then(atividade => {
                        mostrarModalDetalhes(atividade);
                    })
                    .catch(error => {
                        console.error('Erro ao buscar detalhes:', error);
                        alert('Erro ao carregar detalhes da atividade');
                    });
            }

            function renderizarAtividades(atividades) {
                const container = document.getElementById('activitiesList');
                
                if (atividades.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-calendar-times text-3xl mb-2"></i>
                            <p>Nenhuma atividade para esta data</p>
                        </div>
                    `;
                    return;
                }

                let html = '';
                atividades.forEach(atividade => {
                    const corDisciplina = atividade.cor_hex || '#3b82f6';
                    const tipoBadge = getTipoBadge(atividade.tipo_nome);
                    
                    html += `
                        <div class="mb-4 p-3 border-l-4 rounded shadow-sm" style="border-left-color: ${corDisciplina}">
                            <div class="flex justify-between items-start">
                                <h4 class="font-semibold">${atividade.disciplina_nome} - ${atividade.titulo}</h4>
                                <span class="${tipoBadge.classes} text-xs px-2 py-1 rounded">${atividade.tipo_nome}</span>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">${atividade.descricao || 'Sem descrição'}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500">${formatarHorario(atividade)}</span>
                                <button onclick="buscarDetalhesAtividade(${atividade.idatividade})" class="text-blue-600 text-sm hover:underline">Ver detalhes</button>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            }

            function getTipoBadge(tipo) {
                const tipos = {
                    'Prova': { classes: 'bg-blue-100 text-blue-800' },
                    'Trabalho': { classes: 'bg-green-100 text-green-800' },
                    'Atividade': { classes: 'bg-yellow-100 text-yellow-800' },
                    'Avaliação': { classes: 'bg-purple-100 text-purple-800' }
                };
                return tipos[tipo] || { classes: 'bg-gray-100 text-gray-800' };
            }

            function formatarHorario(atividade) {
                if (atividade.horario) {
                    const data = new Date(atividade.horario);
                    return data.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
                return 'Data não definida';
            }

            function mostrarModalDetalhes(atividade) {
                document.getElementById('modalTitle').textContent = `${atividade.disciplina_nome} - ${atividade.titulo}`;
                document.getElementById('modalType').textContent = atividade.tipo_nome;
                document.getElementById('modalSubject').textContent = atividade.disciplina_nome;
                document.getElementById('modalDescription').textContent = atividade.descricao || 'Sem descrição';
                document.getElementById('modalDateTime').textContent = formatarDataHorarioCompleto(atividade);
                document.getElementById('modalTeacher').textContent = atividade.professor_nome || 'Professor não informado';
                
                const tipoBadge = getTipoBadge(atividade.tipo_nome);
                document.getElementById('modalType').className = `text-xs px-2 py-1 rounded mr-2 ${tipoBadge.classes}`;
                
                if (isProfessor) {
                    document.getElementById('editActivityBtn').style.display = 'inline-block';
                    document.getElementById('deleteActivityBtn').style.display = 'inline-block';
                } else {
                    document.getElementById('editActivityBtn').style.display = 'none';
                    document.getElementById('deleteActivityBtn').style.display = 'none';
                }
                
                document.getElementById('activityModal').classList.remove('hidden');
            }

            function formatarDataHorarioCompleto(atividade) {
                if (atividade.horario) {
                    const data = new Date(atividade.horario);
                    return data.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
                return 'Data não definida';
            }

            // Resto do código JavaScript...

            // User menu toggle
            document.getElementById('userMenuButton').addEventListener('click', function() {
                document.getElementById('userMenu').classList.toggle('hidden');
            });

            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                // User menu
                if (!event.target.closest('#userMenuButton')) {
                    document.getElementById('userMenu').classList.add('hidden');
                }
                
                // Activity modal
                if (event.target === document.getElementById('activityModal')) {
                    document.getElementById('activityModal').classList.add('hidden');
                }
                
                // Add activity modal
                if (event.target === document.getElementById('addActivityModal')) {
                    document.getElementById('addActivityModal').classList.add('hidden');
                }
            });

            // Close modal buttons
            document.getElementById('closeModalBtn').addEventListener('click', function() {
                document.getElementById('activityModal').classList.add('hidden');
            });
            
            document.getElementById('closeAddModalBtn').addEventListener('click', function() {
                document.getElementById('addActivityModal').classList.add('hidden');
            });
            
            document.getElementById('cancelAddActivityBtn').addEventListener('click', function() {
                document.getElementById('addActivityModal').classList.add('hidden');
            });

            // Add activity button
            document.getElementById('addActivityBtn').addEventListener('click', function() {
                document.getElementById('addActivityModal').classList.remove('hidden');
            });

            // Save activity button
            document.getElementById('saveActivityBtn').addEventListener('click', function() {
                // In a real application, you would send this data to the server
                // For now, we'll just show an alert and close the modal
                alert('Atividade adicionada com sucesso!');
                document.getElementById('addActivityModal').classList.add('hidden');
                
                // Reset form
                document.getElementById('addActivityForm').reset();
            });

            // Edit and delete buttons in activity modal
            document.getElementById('editActivityBtn').addEventListener('click', function() {
                alert('Funcionalidade de edição será implementada em breve!');
            });
            
            document.getElementById('deleteActivityBtn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja excluir esta atividade?')) {
                    alert('Atividade excluída com sucesso!');
                    document.getElementById('activityModal').classList.add('hidden');
                }
            });

            
        });

        function abrirModalTurmas() {
            document.getElementById('modalTurmas').classList.remove('hidden');
            document.getElementById('userMenu').classList.add('hidden');
        }

        function fecharModalTurmas() {
            document.getElementById('modalTurmas').classList.add('hidden');
        }

        function abrirModalEntrarTurma() {
            document.getElementById('modalEntrarTurma').classList.remove('hidden');
            document.getElementById('userMenu').classList.add('hidden');
            document.getElementById('mensagemErro').classList.add('hidden');
            document.getElementById('mensagemSucesso').classList.add('hidden');
            document.getElementById('codigoTurma').value = '';
        }

        function fecharModalEntrarTurma() {
            document.getElementById('modalEntrarTurma').classList.add('hidden');
        }

        function trocarTurma(turmaId) {
            fetch('trocar_turma.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    turma_id: turmaId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Recarrega a página para atualizar os dados
                } else {
                    alert('Erro ao trocar de turma: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao trocar de turma');
            });
        }

        function entrarTurma() {
            const codigo = document.getElementById('codigoTurma').value.trim();
            const mensagemErro = document.getElementById('mensagemErro');
            const mensagemSucesso = document.getElementById('mensagemSucesso');
            
            if (!codigo) {
                mensagemErro.textContent = 'Por favor, digite o código da turma';
                mensagemErro.classList.remove('hidden');
                return;
            }

            fetch('entrar_turma.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    codigo: codigo
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mensagemSucesso.textContent = data.message || 'Entrou na turma com sucesso!';
                    mensagemSucesso.classList.remove('hidden');
                    mensagemErro.classList.add('hidden');
                    
                    // Recarregar a página após 2 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    mensagemErro.textContent = data.error || 'Erro ao entrar na turma';
                    mensagemErro.classList.remove('hidden');
                    mensagemSucesso.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                mensagemErro.textContent = 'Erro de conexão';
                mensagemErro.classList.remove('hidden');
                mensagemSucesso.classList.add('hidden');
            });
        }

        // Fechar modais ao clicar fora
        window.addEventListener('click', function(event) {
            // User menu
            if (!event.target.closest('#userMenuButton')) {
                document.getElementById('userMenu').classList.add('hidden');
            }
            
            // Modal turmas
            if (event.target === document.getElementById('modalTurmas')) {
                fecharModalTurmas();
            }
            
            // Modal entrar turma
            if (event.target === document.getElementById('modalEntrarTurma')) {
                fecharModalEntrarTurma();
            }
        });
    </script>
</body>
</html>