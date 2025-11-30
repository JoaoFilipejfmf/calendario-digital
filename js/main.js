document.addEventListener('DOMContentLoaded', function() {
    const hasClass = window.AppConfig.usuarioTurmaId;
    console.log(hasClass);
    const isTeacher = window.AppConfig.isAdmin;
    console.log(isTeacher);
    const addBtn = document.getElementById("addActivityBtn");
    
    // ----------------------------------------------------
    // 1. Lógica de Visibilidade do Botão Adicionar Atividade
    // ----------------------------------------------------

    // ----------------------------------------------------
    // 2. Lógica para Usuário sem Turma (Mensagem de Alerta)
    // ----------------------------------------------------
    
    if (!hasClass) {
        document.getElementById('activitiesList').innerHTML = `
             <div class="text-center py-6 text-yellow-600 bg-yellow-50 rounded-lg">
                 <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                 <p class="mb-3">Você não está vinculado a nenhuma turma.</p>
                 <button onclick="ViewManager.switch('view-entrar-turma')" class="text-blue-600 font-bold hover:underline">
                     Entrar em uma turma agora
                 </button>
             </div>
         `;
    }
    // 3. Inicializa o Calendário
    DashboardView.initCalendar();

    // 4. Configuração do Menu de Usuário (Dropdown)
    const userMenuBtn = document.getElementById('userMenuButton');
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            document.getElementById('userMenu').classList.toggle('hidden');
        });
    }

    // Fecha menu ao clicar fora
    window.addEventListener('click', () => {
        const userMenu = document.getElementById('userMenu');
        if(userMenu && !userMenu.classList.contains('hidden')) {
            userMenu.classList.add('hidden');
        }
    });
});