const AddActivityView = {
    // Chamado quando clicamos no botão "Adicionar Atividade" no Dashboard
    open: function() {
        // 1. Limpa o formulário (para não ter dados velhos)
        document.getElementById('addActivityForm').reset();
        
        // 2. Define data padrão como hoje (opcional, boa UX)
        const selectedDate = window.AppConfig.selectedDate;
        
        if (selectedDate) {
            // Preenche o campo de data com o dia selecionado (YYYY-MM-DD)
            document.getElementById('activityDate').value = selectedDate;
            
            // Define um horário padrão (ex: 8:00 AM) para facilitar a entrada
            document.getElementById('activityStartTime').value = '08:00';
            document.getElementById('activityEndTime').value = '09:00';
            
        } else {
            // Fallback: se não houver data selecionada, usa o dia atual
            document.getElementById('activityDate').valueAsDate = new Date();
        }

        // 3. Mostra a tela
        ViewManager.switch('view-add');
    },

    // Chamado quando clicamos no botão "Salvar"
    save: function() {
        // 1. Coleta os dados do DOM
        const data = {
            titulo: document.getElementById('activityTitle').value,
            tipo: document.getElementById('activityType').value,
            disciplina: document.getElementById('activitySubject').value,
            data: document.getElementById('activityDate').value,
            inicio: document.getElementById('activityStartTime').value,
            fim: document.getElementById('activityEndTime').value,
            descricao: document.getElementById('activityDescription').value,
            // Importante: garantir que estamos enviando o ID da turma atual
            turma_id: window.AppConfig.usuarioTurmaId 
        };

        // 2. Validação básica (Client-side)
        if (!data.titulo || !data.data || !data.disciplina) {
            alert('Por favor, preencha os campos obrigatórios (Título, Disciplina e Data).');
            return;
        }

        // 3. Muda botão para estado de "Salvando..." (UX)
        const btnSalvar = document.getElementById('saveActivityBtn');
        const textoOriginal = btnSalvar.textContent;
        btnSalvar.textContent = 'Salvando...';
        btnSalvar.disabled = true;

        // 4. Envia para o DataService
        DataService.salvarAtividade(data)
            .then(response => {
                if (response.success) {
                    alert('Atividade salva com sucesso!');
                    // Volta pro dashboard
                    ViewManager.switch('view-dashboard');
                    // Recarrega o calendário para mostrar a nova atividade
                    location.reload(); 
                } else {
                    alert('Erro ao salvar: ' + (response.error || 'Erro desconhecido'));
                }
            })
            .catch(error => {
                alert('Erro de conexão: ' + error.message);
            })
            .finally(() => {
                // Restaura o botão
                btnSalvar.textContent = textoOriginal;
                btnSalvar.disabled = false;
            });
    }
};