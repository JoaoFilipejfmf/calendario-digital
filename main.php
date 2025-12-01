<?php
require_once 'conexao.php';
require_once 'class/rb-mysql.php';

session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

// Buscar informações do usuário e sua turma
$usuario = $_SESSION['usuario'];

// Buscar turma do usuário

// SQL com LEFT JOIN:
// 1. Seleciona todas as colunas de 'turma'.
// 2. Seleciona a coluna 'administrador' da tabela de ligação e a renomeia para 'is_admin'.
// 3. O LEFT JOIN garante que todas as turmas apareçam, mesmo sem correspondência na participacaoturma.
// 4. A condição de JOIN é filtrada pelo ID do usuário específico.
require_once "carregar_turma_sessao.php";

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCalendar</title>

    <script src="js/tailwind.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
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
    </style>
</head>

<script>
    function minhaf() {
        console.log("<?= count($turmas_usuario) > 0 ? "boa" : "vish" ?>");
    }
</script>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-2xl mr-3"></i>
                <h1 class="text-xl font-bold">Calendário Escolar Digital</h1>
                <?php if (isset($turma)): ?>
                    <span id="header-class-name-badge" class="ml-4 text-sm bg-blue-500 px-2 py-1 rounded">Turma: <?= htmlspecialchars($turma['nome']) ?></span>
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
                        <a href="perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="ViewManager.switch('view-turmas')">
                            Turma: <span id="header-class-name-dropdown"><?= $possui_turma ? htmlspecialchars($turma['nome']) : "Não definida" ?></span>
                        </a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">

        <section id="view-dashboard" class="view-section">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="lg:w-2/3 bg-white rounded-lg shadow-md p-4">
                    <div id="calendar"></div>
                </div>

                <div class="lg:w-1/3 bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-xl font-bold mb-4">Detalhes do Dia</h2>
                    <button onclick="ClassSettingsView.openSettings()" class="text-gray-500 hover:text-gray-800" title="Ver Participantes / Configurações">
                        <i class="fas fa-cog text-xl"></i>
                    </button>
                    <div class="mb-6">
                        <h3 id="selectedDate" class="text-lg font-semibold text-blue-600">Selecione uma data</h3>
                    </div>

                    <div id="activitiesList">
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-calendar-day text-3xl mb-2"></i>
                            <p>Selecione uma data para ver as atividades</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button onclick="AddActivityView.open()" id="addActivityBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center justify-center hidden">
                            <i class="fas fa-plus mr-2"></i> Adicionar Atividade
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section id="view-details" class="view-section hidden">
            <div class="bg-white rounded-lg shadow-md max-w-4xl mx-auto">
                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-lg">
                    <h3 class="text-xl font-bold text-gray-800">Detalhes da Atividade</h3>
                    <button onclick="ViewManager.switch('view-dashboard')" class="text-gray-600 hover:text-blue-600 flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </button>
                </div>

                <div class="p-6">
                    <div class="mb-6">
                        <h4 id="viewTitle" class="text-2xl font-bold mb-3 text-gray-900"></h4>
                        <div class="flex items-center mb-2">
                            <span id="viewType" class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full mr-3"></span>
                            <span id="viewSubject" class="text-base text-gray-600 font-medium"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <h5 class="font-semibold mb-2 text-gray-500 uppercase text-sm">Descrição</h5>
                            <p id="viewDescription" class="text-gray-800 leading-relaxed"></p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h5 class="font-semibold mb-1 text-gray-500 uppercase text-sm">Data e Horário</h5>
                                <p id="viewDateTime" class="text-gray-800 font-medium"></p>
                            </div>
                            <div>
                                <h5 class="font-semibold mb-1 text-gray-500 uppercase text-sm">Professor</h5>
                                <p id="viewTeacher" class="text-gray-800 font-medium"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end p-6 border-t bg-gray-50 rounded-b-lg gap-3">
                    <button id="editActivityBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-200">
                        Editar
                    </button>
                    <button id="deleteActivityBtn" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-6 rounded-md transition duration-200">
                        Excluir
                    </button>
                </div>
            </div>
        </section>

        <section id="view-add" class="view-section hidden">
            <div class="bg-white rounded-lg shadow-md max-w-3xl mx-auto">
                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-lg">
                    <h3 class="text-xl font-bold text-gray-800">Nova Atividade</h3>
                    <button onclick="ViewManager.switch('view-dashboard')" class="text-gray-600 hover:text-blue-600 flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </button>
                </div>

                <form id="addActivityForm" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="activityTitle">
                                    Título da Atividade
                                </label>
                                <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    id="activityTitle" type="text" placeholder="Ex: Prova de Matemática">
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="activityType">
                                    Tipo de Atividade
                                </label>
                                <select class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                                <select class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    id="activitySubject">
                                    <option value="Matemática">Matemática</option>
                                    <option value="Português">Português</option>
                                    <option value="História">História</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="activityDate">
                                    Data
                                </label>
                                <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    id="activityDate" type="date">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activityStartTime">
                                        Início
                                    </label>
                                    <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        id="activityStartTime" type="time">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2" for="activityEndTime">
                                        Término
                                    </label>
                                    <input class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        id="activityEndTime" type="time">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="activityDescription">
                            Descrição Detalhada
                        </label>
                        <textarea class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                            id="activityDescription" rows="4" placeholder="Descreva os detalhes da atividade..."></textarea>
                    </div>
                </form>

                <div class="flex justify-end p-6 border-t bg-gray-50 rounded-b-lg gap-3">
                    <button onclick="ViewManager.switch('view-dashboard')" class="bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-medium py-2 px-6 rounded-md transition duration-200">
                        Cancelar
                    </button>
                    <button id="saveActivityBtn" onclick="AddActivityView.save()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition duration-200 shadow-sm">
                        Salvar Atividade
                    </button>
                </div>
            </div>
        </section>

        <section id="view-turmas" class="view-section hidden">
            <div class="bg-white rounded-lg shadow-md max-w-2xl mx-auto mt-6">
                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-lg">
                    <h3 class="text-xl font-bold text-gray-800">Minhas Turmas</h3>
                    <button onclick="ViewManager.switch('view-dashboard')" class="text-gray-600 hover:text-blue-600 flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-gray-600 mb-6">Selecione a turma que deseja visualizar:</p>
                    <div id="listaTurmas" class="grid grid-cols-1 gap-4">
                        <?php if (count($turmas_usuario) > 0) { ?>
                            <?php foreach ($turmas_usuario as $t) { ?>
                                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition <?= $turma['id'] == $t['id'] ? 'bg-blue-50 border-blue-200' : '' ?>">
                                    <div>
                                        <h4 class="font-bold text-lg"><?= htmlspecialchars($t['nome']) ?></h4>
                                        <?php if ($turma['codigo']) { ?>
                                            <p class="text-sm text-gray-500">Código: <span class="font-mono bg-gray-100 px-2 py-0.5 rounded"><?= htmlspecialchars($t['codigo']) ?></span></p>
                                        <?php } ?>
                                    </div>

                                    <?php if ($turma['id'] == $t['id']) { ?>
                                        <span class="bg-green-100 text-green-800 text-sm font-bold px-3 py-1 rounded-full">Atual</span>
                                    <?php } else { ?>
                                        <button onclick="TurmasView.trocar(<?= $t['id'] ?>)" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded shadow-sm">
                                            Selecionar
                                        </button>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-users-slash text-4xl text-gray-400 mb-3"></i>
                                <p class="text-gray-600">Você não está em nenhuma turma</p>
                            </div>
                        <?php } ?>
                    </div>
                    <button onclick="ClassSettingsView.openCreate()" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded flex items-center mt-4">
                        <i class="fas fa-plus mr-2"></i> Nova Turma
                    </button>
                    <button onclick="ViewManager.switch('view-entrar-turma')" class="bg-green-600 hover:bg-green-700 text-white text-sm font-bold py-2 px-4 rounded flex items-center mt-4">
                        <i class="fas fa-plus mr-2"></i> Entrar em uma turma já existente
                    </button>
                </div>
            </div>
        </section>

        <section id="view-entrar-turma" class="view-section hidden">
            <div class="bg-white rounded-lg shadow-md max-w-xl mx-auto mt-6">
                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-lg">
                    <h3 class="text-xl font-bold text-gray-800">Entrar em uma Nova Turma</h3>
                    <button onclick="ViewManager.switch('view-dashboard')" class="text-gray-600 hover:text-blue-600 flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </button>
                </div>

                <form id="formEntrarTurma" class="p-8">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="codigoTurma">
                            Código de Acesso
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input class="pl-10 shadow-sm appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                id="codigoTurma" type="text" placeholder="Ex: A1B2C3" required>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Peça o código para o professor da turma.</p>
                    </div>

                    <div id="mensagemErro" class="hidden mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded"></div>
                    <div id="mensagemSucesso" class="hidden mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded"></div>

                    <button type="button" onclick="TurmasView.entrar()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded shadow-md transition duration-200">
                        Entrar na Turma
                    </button>
                </form>
            </div>
        </section>

        <section id="view-create-class" class="view-section hidden">
            <div class="bg-white rounded-lg shadow-md max-w-md mx-auto mt-6">
                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-lg">
                    <h3 class="text-xl font-bold text-gray-800">Criar Nova Turma</h3>
                    <button onclick="ViewManager.switch('view-turmas')" class="text-gray-600 hover:text-blue-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="formCreateClass" class="p-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nome da Turma</label>
                        <input id="newClassName" type="text" class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ex: Matemática 2024" required>
                    </div>
                    <button type="button" onclick="ClassSettingsView.create()" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700">
                        Criar turma
                    </button>
                </form>
            </div>
        </section>

        <section id="view-class-settings" class="view-section hidden">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl mx-auto mt-4">

                <div class="flex justify-between items-center p-6 border-b bg-gray-50 rounded-t-lg">
                    <h3 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-cog mr-2"></i> Configurações da Turma
                    </h3>
                    <button onclick="ViewManager.switch('view-dashboard')" class="text-gray-600 hover:text-blue-600 flex items-center gap-2 font-medium">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </button>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="md:col-span-2">
                            <label class="text-xs text-gray-500 uppercase font-bold tracking-wider">Nome da Turma</label>
                            <div class="flex gap-2 mt-1">
                                <input id="settingsClassName" type="text"
                                    class="w-full text-lg font-bold p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:cursor-not-allowed transition duration-200"
                                    disabled>
                                <button id="btnSaveName" onclick="ClassSettingsView.updateName()" class="hidden bg-green-500 text-white px-3 rounded hover:bg-green-600 shadow-sm">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500 uppercase font-bold tracking-wider">Código de Convite</label>
                            <div class="mt-1 flex items-center bg-blue-50 border border-blue-100 rounded p-2 justify-between">
                                <span id="settingsClassCode" class="font-mono text-blue-800 font-bold tracking-wider text-lg"></span>
                                <button onclick="navigator.clipboard.writeText(document.getElementById('settingsClassCode').innerText).then(()=>alert('Copiado!'))"
                                    class="text-blue-400 hover:text-blue-600 p-1">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100 my-6">

                    <div class="mb-8">
                        <h4 class="text-lg font-bold mb-4 text-gray-700">Participantes</h4>
                        <div class="bg-white border rounded-lg overflow-hidden">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold">
                                    <tr>
                                        <th class="p-4">Usuário</th>
                                        <th class="p-4">Permissão</th>
                                        <th class="p-4 text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="membersListBody" class="divide-y divide-gray-100 text-sm">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div id="dangerZone" class="hidden mt-10 pt-6 border-t border-red-100">
                        <h4 class="text-red-600 font-bold mb-3 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Zona de Perigo
                        </h4>
                        <div class="flex flex-col md:flex-row justify-between items-center bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="mb-4 md:mb-0">
                                <p class="font-bold text-red-800">Excluir esta turma</p>
                                <p class="text-xs text-red-600">Esta ação apagará permanentemente todas as atividades e removerá todos os membros. Não pode ser desfeita.</p>
                            </div>
                            <button onclick="ClassSettingsView.deleteClass()" class="bg-white border border-red-300 text-red-600 hover:bg-red-600 hover:text-white px-5 py-2 rounded-md font-medium transition duration-200 shadow-sm">
                                Excluir Turma
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <script>
        window.AppConfig = {
            usuarioTurmaId: <?= $possui_turma ? $turma['id'] : 0 ?>,
            isAdmin: <?= $possui_turma ? ($isAdmin ? "true" : "false") : "false" ?> // Ou injecte via PHP
        };
    </script>

    <script src="js/view_manager.js"></script>
    <script src="js/data_service.js"></script>
    <script src="js/turmas_view.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/main.js"></script>
    <script src="js/activity_view.js"></script>
    <script src="js/add_activity_view.js"></script>
    <script src="js/class_settings_view.js"></script>
</body>

</html>