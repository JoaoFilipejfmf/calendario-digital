const ActivityView = {
    // Função principal chamada ao clicar num evento no calendário
    show: function(idAtividade) {
        // 1. Busca os dados (chama o backend)
        DataService.buscarDetalhes(idAtividade)
            .then(atividade => {
                // 2. Preenche a tela
                this.render(atividade);
                // 3. Troca a visualização para a tela de detalhes
                ViewManager.switch('view-details');
            })
            .catch(error => {
                console.error(error);
                alert('Erro ao carregar os detalhes da atividade.');
            });
    },

    // Função interna para preencher o HTML com os dados
    render: function(atividade) {
        // Preenche textos básicos
        document.getElementById('viewTitle').textContent = `${atividade.disciplina} - ${atividade.titulo}`;
        document.getElementById('viewSubject').textContent = atividade.disciplina;
        document.getElementById('viewDescription').textContent = atividade.descricao || 'Sem descrição informada.';
        document.getElementById('viewTeacher').textContent = atividade.professor_nome || 'Professor não informado';
        
        // Formata e preenche a data
        document.getElementById('viewDateTime').textContent = this.formatDate(atividade.horario_inicio);

        // Configura o Badge (Etiqueta) de tipo (Prova, Trabalho, etc)
        const badgeEl = document.getElementById('viewType');
        badgeEl.textContent = atividade.tipo;
        // Limpa classes anteriores e adiciona as novas
        badgeEl.className = `text-sm px-3 py-1 rounded-full mr-3 ${this.getBadgeClasses(atividade.tipo)}`;

        // Configura botões de ação (Editar/Excluir)
        this.setupActionButtons(atividade);
    },

    // Configura os botões de Editar e Excluir
    setupActionButtons: function(atividade) {
        const btnDelete = document.getElementById('deleteActivityBtn');
        const btnEdit = document.getElementById('editActivityBtn');

        // Se for professor, configura os cliques. Se não, esconde.
        if (window.AppConfig.isProfessor) {
            btnDelete.classList.remove('hidden');
            btnEdit.classList.remove('hidden');

            // Remove listeners antigos para evitar duplicação (cloneNode hack)
            // Ou simplesmente sobrescreve o onclick
            btnDelete.onclick = () => this.handleDelete(atividade.id);
            btnEdit.onclick = () => alert(`Editar atividade ${atividade.id} (Implementar lógica de edição)`);
        } else {
            btnDelete.classList.add('hidden');
            btnEdit.classList.add('hidden');
        }
    },

    // Lógica para excluir
    handleDelete: function(id) {
        if (confirm('Tem certeza que deseja excluir esta atividade?')) {
            // Aqui você chamaria o DataService.excluir(id) - (precisaria criar essa função lá)
            alert('Simulação: Atividade ' + id + ' excluída.');
            // Volta para o dashboard
            ViewManager.switch('view-dashboard');
            // Recarrega o calendário (se necessário)
            location.reload(); 
        }
    },

    // Helper: Formata a data (trazido do seu código original)
    formatDate: function(dateString) {
        if (!dateString) return 'Data não definida';
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    },

    // Helper: Define cores das etiquetas (trazido do seu código original)
    getBadgeClasses: function(tipo) {
        const tipos = {
            'Prova': 'bg-blue-100 text-blue-800',
            'Trabalho': 'bg-green-100 text-green-800',
            'Atividade': 'bg-yellow-100 text-yellow-800',
            'Avaliação': 'bg-purple-100 text-purple-800'
        };
        return tipos[tipo] || 'bg-gray-100 text-gray-800';
    }
};