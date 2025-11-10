<?php
require_once 'conexao.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Buscar informações do usuário e sua turma
$usuario_id = $_SESSION['usuario_id'];
$usuario = R::getRow("SELECT * FROM usuario WHERE idusuario = ?", [$usuario_id]);

// Buscar turma do usuário
$turma_usuario = R::getRow("
    SELECT t.* 
    FROM turma t 
    INNER JOIN usuario_participa_de_turma upt ON t.idturma = upt.turma_idturma 
    WHERE upt.usuario_idusuario = ? 
    LIMIT 1
", [$usuario_id]);

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
    <!-- ... código dos modais ... -->

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
                events: 'buscar_atividades.php?turma_id=' + (usuarioTurmaId || ''),
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
        });
    </script>
</body>
</html>