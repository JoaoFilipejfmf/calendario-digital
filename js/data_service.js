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

    async buscarAtividades(data, turmaId) {
        const response = await fetch(`buscar_atividades_data.php?data=${data}&turma_id=${turmaId}`);
        return await response.json();
    },

    async buscarDetalhes(idAtividade) {
        const response = await fetch(`buscar_detalhes_atividade.php?id=${idAtividade}`);
        return await response.json();
    }
};