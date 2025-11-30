const DashboardView = {
    initCalendar: function () {
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
            events: async function (fetchInfo, successCallback, failureCallback) {
                const turmaId = window.AppConfig.usuarioTurmaId;

                if (!turmaId) {
                    // Não tem turma, não carrega eventos.
                    successCallback([]);
                    return;
                }

                try {
                    // Esta função do DataService deve retornar os dados no formato FullCalendar (id, title, start)
                    const eventos = await DataService.buscarTodosEventos(turmaId);
                    successCallback(eventos);
                } catch (error) {
                    console.error("Erro ao carregar eventos:", error);
                    failureCallback(error);
                }
            },

            dateClick: (info) => this.handleDateClick(info),
            eventClick: (info) => this.handleEventClick(info)
        });
        calendar.render();
        console.log(calendar.events);
    },

    handleDateClick: function (info) {
        window.AppConfig.selectedDate = info.dateStr;

        const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('selectedDate').textContent = info.date.toLocaleDateString('pt-BR', dateOptions);

        DataService.buscarAtividades(info.dateStr)
            .then(atividades => this.renderList(atividades))
            .catch(console.error);

        const addBtn = document.getElementById("addActivityBtn");
        const isTeacher = window.AppConfig.isAdmin;
        const hasClass = window.AppConfig.usuarioTurmaId;

        if (addBtn) {
            if (isTeacher && hasClass) {
                // Se é professor, tem turma, E selecionou a data: mostra o botão
                addBtn.classList.remove("hidden");
            } else {
                // Caso contrário (estudante ou sem turma): esconde
                addBtn.classList.add("hidden");
            }
        }
    },

    handleEventClick: function (info) {
        ActivityView.show(info.event.id);
    },

    renderList: function (atividades) {
        const container = document.getElementById('activitiesList');
        if (atividades.length === 0) {
            container.innerHTML = `<div class="text-center py-4 text-gray-500"><p>Nenhuma atividade.</p></div>`;
            return;
        }

        let html = atividades.map(atv => this.createActivityCard(atv)).join('');
        container.innerHTML = html;
    },

    createActivityCard: function (atv) {
        // Lógica de HTML do card (simplificada para brevidade)
        return `
            <div class="mb-4 p-3 border-l-4 rounded shadow-sm bg-white" style="border-left-color: ${atv.cor_hex || '#3b82f6'}">
                <div class="flex justify-between">
                    <h4 class="font-bold">${atv.disciplina}</h4>
                    <span class="text-xs bg-gray-100 px-2 py-1 rounded">${atv.tipo}</span>
                </div>
                <p>${atv.titulo}</p>
            </div>
        `;
    }
};