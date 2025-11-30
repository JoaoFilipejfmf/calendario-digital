const DashboardView = {
    initCalendar: function() {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) return;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek' // Simplificado para mobile
            },
            // Usa a variável global configurada no PHP
            events: window.AppConfig.usuarioTurmaId ? 
                   `buscar_atividades.php?turma_id=${window.AppConfig.usuarioTurmaId}` : [],
            
            dateClick: (info) => this.handleDateClick(info),
            eventClick: (info) => this.handleEventClick(info)
        });
        calendar.render();
    },

    handleDateClick: function(info) {
        const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('selectedDate').textContent = info.date.toLocaleDateString('pt-BR', dateOptions);

        if (window.AppConfig.usuarioTurmaId) {
            DataService.buscarAtividades(info.dateStr, window.AppConfig.usuarioTurmaId)
                .then(atividades => this.renderList(atividades))
                .catch(console.error);
        }
    },

    handleEventClick: function(info) {
        // Assume que existe um ActivityView (das interações anteriores)
        // Se não tiver modularizado ActivityView ainda, a lógica iria aqui.
        // Exemplo: ActivityView.showDetails(info.event.id);
        console.log("Evento clicado:", info.event.id);
    },

    renderList: function(atividades) {
        const container = document.getElementById('activitiesList');
        if (atividades.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-gray-500"><p>Nenhuma atividade.</p></div>`;
            return;
        }
        
        let html = atividades.map(atv => this.createActivityCard(atv)).join('');
        container.innerHTML = html;
    },

    createActivityCard: function(atv) {
        // Lógica de HTML do card (simplificada para brevidade)
        return `
            <div class="mb-4 p-3 border-l-4 rounded shadow-sm bg-white" style="border-left-color: ${atv.cor_hex || '#3b82f6'}">
                <div class="flex justify-between">
                    <h4 class="font-bold">${atv.disciplina_nome}</h4>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">${atv.tipo_nome}</span>
                </div>
                <p>${atv.titulo}</p>
            </div>
        `;
    }
};