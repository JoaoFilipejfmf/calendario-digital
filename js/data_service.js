const DataService = {
    async trocarTurma(turmaId) {
        try {
            const response = await fetch('trocar_turma.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ turma_id: turmaId })
            });
            return await response.json();
        } catch (error) {
            throw new Error('Erro de conexão ao trocar turma');
        }
    },

    async entrarTurma(codigo) {
        try {
            const response = await fetch('entrar_turma.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ codigo: codigo })
            });
            return await response.json();
        } catch (error) {
            throw new Error('Erro de conexão ao entrar na turma');
        }
    },

    async buscarAtividades(data) {
        const response = await fetch(`buscar_atividades_data.php?data=${data}`);
        return await response.json();
    },

    // Novo método para o FullCalendar
    async buscarTodosEventos(turmaId) {
        // Nota: O FullCalendar envia as datas de início e fim no 'fetchInfo', 
        // mas para simplificar, você pode buscar todos os eventos futuros.

        // Você precisará de um novo endpoint PHP que receba apenas o turmaId
        const response = await fetch(`buscar_atividades.php`);
        if (!response.ok) {
            throw new Error('Falha ao carregar eventos da turma.');
        }

        const atividades = await response.json();

        // Mapeamento para o formato FullCalendar é crucial
        return atividades.map(ativ => ({
            id: ativ.id,
            title: ativ.titulo,
            start: ativ.horario_inicio,
            end: ativ.horario_fim,
            
            // ... (outros campos)
        }));
    },

    async buscarDetalhes(idAtividade) {
        const response = await fetch(`buscar_detalhes_atividade.php?id=${idAtividade}`);
        return await response.json();
    },

    async salvarAtividade(atividadeData) {
        try {
            // Supondo que você tenha um arquivo PHP para salvar
            const response = await fetch('salvar_atividade.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(atividadeData)
            });
            return await response.json();
        } catch (error) {
            throw new Error('Erro ao conectar com o servidor para salvar.');
        }
    }
};