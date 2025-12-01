const ActivityView = {
    currentActivity: null,
    // Função principal chamada ao clicar num evento no calendário
    show: function(idAtividade) {
        DataService.buscarDetalhes(idAtividade)
            .then(atividade => {
                this.currentActivity = atividade;
                this.render(atividade);
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
        const btnEdit = document.getElementById('editActivityBtn');
        const btnDelete = document.getElementById('deleteActivityBtn');
        const myId = window.AppConfig.usuarioId; // Certifique-se que isso existe
        const isAdmin = window.AppConfig.isAdmin;

        // Se for professor, configura os cliques. Se não, esconde.
        if (atividade.criado_por == myId || isAdmin) {
            btnEdit.classList.remove('hidden');
            btnDelete.classList.remove('hidden');
            
            // Atribui as ações
            btnEdit.onclick = () => this.handleEdit();
            btnDelete.onclick = () => this.handleDelete();
        } else {
            btnEdit.classList.add('hidden');
            btnDelete.classList.add('hidden');
        }
    },

    // Lógica para excluir
    handleDelete: async function() {
        if(!confirm("Tem certeza que deseja excluir esta atividade?")) return;

        try {
            const response = await fetch('excluir_atividade.php', {
                method: 'POST',
                body: JSON.stringify({ id: this.currentActivity.id }) // ou idatividade dependendo do seu banco
            });
            const data = await response.json();
            
            if(data.success) {
                alert("Atividade excluída.");
                ViewManager.switch('view-dashboard');
                location.reload(); // Recarrega calendário
            } else {
                alert(data.error);
            }
        } catch(e) { console.error(e); }
    },

    handleEdit: function() {
        // Chama o formulário de adição, mas em "modo edição"
        // Precisamos criar essa função no AddActivityView
        AddActivityView.edit(this.currentActivity);
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