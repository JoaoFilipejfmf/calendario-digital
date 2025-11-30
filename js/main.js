document.addEventListener('DOMContentLoaded', function() {
    // 1. Inicializa o Calendário
    DashboardView.initCalendar();

    // 2. Verifica estado inicial (sem turma?)
    if (!window.AppConfig.usuarioTurmaId) {
        document.getElementById('activitiesList').innerHTML = `
            <div class="text-center py-6 text-yellow-600 bg-yellow-50 rounded-lg">
                <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                <p class="mb-3">Você não está vinculado a nenhuma turma.</p>
                <button onclick="ViewManager.switch('view-entrar-turma')" class="text-blue-600 font-bold hover:underline">
                    Entrar em uma turma agora
                </button>
            </div>
        `;
        
        const addBtn = document.getElementById("addActivityBtn");
        if(addBtn) addBtn.classList.add("hidden");
    }

    // 3. Verifica permissões de professor
    if (!window.AppConfig.isProfessor) {
        const addBtn = document.getElementById("addActivityBtn");
        if(addBtn) addBtn.classList.add("hidden");
    }

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