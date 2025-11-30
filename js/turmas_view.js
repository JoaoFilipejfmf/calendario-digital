const TurmasView = {
    trocar: function(turmaId) {
        DataService.trocarTurma(turmaId)
            .then(data => {
                if (data.success) {
                    location.reload(); // Recarrega para PHP atualizar sessão/calendário
                } else {
                    alert('Erro: ' + data.error);
                }
            })
            .catch(err => alert(err.message));
    },

    entrar: function() {
        const codigoInput = document.getElementById('codigoTurma');
        const codigo = codigoInput.value.trim();
        const msgErro = document.getElementById('mensagemErro');
        const msgSucesso = document.getElementById('mensagemSucesso');

        if (!codigo) {
            msgErro.textContent = 'Digite o código.';
            msgErro.classList.remove('hidden');
            return;
        }

        DataService.entrarTurma(codigo)
            .then(data => {
                if (data.success) {
                    msgSucesso.textContent = data.message || 'Sucesso!';
                    msgSucesso.classList.remove('hidden');
                    msgErro.classList.add('hidden');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    msgErro.textContent = data.error || 'Erro desconhecido.';
                    msgErro.classList.remove('hidden');
                    msgSucesso.classList.add('hidden');
                }
            })
            .catch(err => {
                msgErro.textContent = err.message;
                msgErro.classList.remove('hidden');
            });
    }
};