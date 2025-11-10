<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário Escolar Digital</title>
    
    <!-- Tailwind CSS -->
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
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-2xl mr-3"></i>
                <h1 class="text-xl font-bold">Calendário Escolar Digital</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span class="hidden md:inline">Olá, Professor Silva</span>
                <div class="relative">
                    <button id="userMenuButton" class="flex items-center focus:outline-none">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <i class="fas fa-chevron-down ml-1 text-sm"></i>
                    </button>
                    <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                        <a href="perfil.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurações</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sair</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - Calendar -->
            <div class="lg:w-2/3 bg-white rounded-lg shadow-md p-4">
                <div id="calendar"></div>
            </div>

            <!-- Right Column - Day Details -->
            <div class="lg:w-1/3 bg-white rounded-lg shadow-md p-4">
                <h2 class="text-xl font-bold mb-4">Detalhes do Dia</h2>
                
                <!-- Selected Date -->
                <div class="mb-6">
                    <h3 id="selectedDate" class="text-lg font-semibold text-blue-600">15 de Outubro de 2023</h3>
                </div>
                
                <!-- Activities List -->
                <div id="activitiesList">
                    <div class="mb-4 p-3 border-l-4 subject-color-1 rounded shadow-sm">
                        <div class="flex justify-between items-start">
                            <h4 class="font-semibold">Matemática - Prova Bimestral</h4>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Avaliação</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Conteúdo: Funções, equações e geometria analítica</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500">08:00 - 10:00</span>
                            <button class="text-blue-600 text-sm hover:underline">Ver detalhes</button>
                        </div>
                    </div>
                    
                    <div class="mb-4 p-3 border-l-4 subject-color-3 rounded shadow-sm">
                        <div class="flex justify-between items-start">
                            <h4 class="font-semibold">Biologia - Trabalho em Grupo</h4>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Trabalho</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Apresentação sobre ecossistemas brasileiros</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500">Entrega: 23:59</span>
                            <button class="text-blue-600 text-sm hover:underline">Ver detalhes</button>
                        </div>
                    </div>
                    
                    <div class="mb-4 p-3 border-l-4 subject-color-4 rounded shadow-sm">
                        <div class="flex justify-between items-start">
                            <h4 class="font-semibold">História - Atividade para Entrega</h4>
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Atividade</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Resenha sobre Revolução Industrial</p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-xs text-gray-500">Entrega: 18:00</span>
                            <button class="text-blue-600 text-sm hover:underline">Ver detalhes</button>
                        </div>
                    </div>
                </div>
                
                <!-- Add Activity Button (for teachers) -->
                <div class="mt-6">
                    <button id="addActivityBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> Adicionar Atividade
                    </button>
                </div>
            </div>
        </div>
    </main>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize FullCalendar
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    {
                        title: 'Matemática - Prova',
                        start: '2023-10-15',
                        color: '#3b82f6',
                        extendedProps: {
                            type: 'Avaliação',
                            subject: 'Matemática',
                            description: 'Conteúdo: Funções, equações e geometria analítica. A prova terá duração de 2 horas e será composta por questões objetivas e dissertativas.',
                            time: '08:00 - 10:00',
                            teacher: 'Prof. Silva'
                        }
                    },
                    {
                        title: 'Biologia - Trabalho',
                        start: '2023-10-15',
                        color: '#10b981',
                        extendedProps: {
                            type: 'Trabalho',
                            subject: 'Biologia',
                            description: 'Apresentação sobre ecossistemas brasileiros. Grupos de 4 alunos.',
                            time: 'Entrega: 23:59',
                            teacher: 'Prof. Santos'
                        }
                    },
                    {
                        title: 'História - Atividade',
                        start: '2023-10-15',
                        color: '#f59e0b',
                        extendedProps: {
                            type: 'Atividade',
                            subject: 'História',
                            description: 'Resenha sobre Revolução Industrial. Mínimo de 2 páginas.',
                            time: 'Entrega: 18:00',
                            teacher: 'Prof. Oliveira'
                        }
                    },
                    {
                        title: 'Física - Prova',
                        start: '2023-10-20',
                        color: '#8b5cf6',
                        extendedProps: {
                            type: 'Avaliação',
                            subject: 'Física',
                            description: 'Conteúdo: Cinemática e Dinâmica.',
                            time: '10:00 - 12:00',
                            teacher: 'Prof. Lima'
                        }
                    },
                    {
                        title: 'Português - Trabalho',
                        start: '2023-10-25',
                        color: '#ef4444',
                        extendedProps: {
                            type: 'Trabalho',
                            subject: 'Português',
                            description: 'Análise do livro "Dom Casmurro".',
                            time: 'Entrega: 23:59',
                            teacher: 'Prof. Costa'
                        }
                    }
                ],
                dateClick: function(info) {
                    // Update selected date display
                    const date = new Date(info.dateStr);
                    const options = { day: 'numeric', month: 'long', year: 'numeric' };
                    document.getElementById('selectedDate').textContent = date.toLocaleDateString('pt-BR', options);
                    
                    // In a real application, you would fetch activities for this date from the server
                    // For now, we'll just show a message
                    document.getElementById('activitiesList').innerHTML = `
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-calendar-day text-3xl mb-2"></i>
                            <p>Clique em um evento no calendário para ver os detalhes</p>
                        </div>
                    `;
                },
                eventClick: function(info) {
                    const event = info.event;
                    
                    // Update modal content
                    document.getElementById('modalTitle').textContent = event.title;
                    document.getElementById('modalType').textContent = event.extendedProps.type;
                    document.getElementById('modalSubject').textContent = event.extendedProps.subject;
                    document.getElementById('modalDescription').textContent = event.extendedProps.description;
                    document.getElementById('modalDateTime').textContent = `${event.start.toLocaleDateString('pt-BR')}, ${event.extendedProps.time}`;
                    document.getElementById('modalTeacher').textContent = event.extendedProps.teacher;
                    
                    // Update type badge color based on type
                    const typeBadge = document.getElementById('modalType');
                    typeBadge.className = 'text-xs px-2 py-1 rounded mr-2';
                    
                    if (event.extendedProps.type === 'Avaliação') {
                        typeBadge.classList.add('bg-blue-100', 'text-blue-800');
                    } else if (event.extendedProps.type === 'Trabalho') {
                        typeBadge.classList.add('bg-green-100', 'text-green-800');
                    } else if (event.extendedProps.type === 'Atividade') {
                        typeBadge.classList.add('bg-yellow-100', 'text-yellow-800');
                    }
                    
                    // Show modal
                    document.getElementById('activityModal').classList.remove('hidden');
                }
            });
            
            calendar.render();

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
    </script>
</body>
</html>