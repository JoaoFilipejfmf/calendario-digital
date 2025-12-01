const ClassSettingsView = {
    currentTurmaId: null,

    openCreate: function () {
        document.getElementById('formCreateClass').reset();
        ViewManager.switch('view-create-class');
    },

    create: async function () {
        const nome = document.getElementById('newClassName').value;

        if (!nome) return alert("O nome é obrigatório");

        try {
            const response = await fetch('salvar_turma.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nome })
            });
            const data = await response.json();

            if (data.success) {
                window.AppConfig.usuarioTurmaId = data.id;
                window.AppConfig.isAdmin = true;
                alert("Turma criada!");
                // Recarrega a página para atualizar tudo ou troca para o dashboard da nova turma
                location.reload();
            } else {
                alert("Erro: " + data.error);
            }
        } catch (e) { console.error(e); }
    },

    openSettings: function () {
        // Pega do estado global
        this.currentTurmaId = window.AppConfig.usuarioTurmaId;
        if (!this.currentTurmaId) return alert("Selecione uma turma primeiro.");

        this.loadData();
        ViewManager.switch('view-class-settings');
    },

    loadData: async function () {
        try {
            const response = await fetch(`buscar_detalhes_turma.php?turma_id=${this.currentTurmaId}`);
            const data = await response.json();

            if (data.success) {
                this.render(data.turma, data.membros, data.isCriador);
            } else {
                alert(data.error);
            }
        } catch (e) { console.error(e); }
    },

    render: function (turma, membros, isCriador) {
        // 1. Header e Nome
        const nameInput = document.getElementById('settingsClassName');
        nameInput.value = turma.nome;
        document.getElementById('settingsClassCode').textContent = turma.codigo;

        // Controle de Edição de Nome
        const btnSaveName = document.getElementById('btnSaveName');
        const dangerZone = document.getElementById('dangerZone');

        if (isCriador) {
            nameInput.disabled = false;
            nameInput.classList.add('border-gray-300', 'bg-gray-50');
            nameInput.classList.remove('bg-transparent', 'border-transparent');
            dangerZone.classList.remove('hidden');

            nameInput.oninput = () => btnSaveName.classList.remove('hidden');
        } else {
            nameInput.disabled = true;
            nameInput.classList.add('bg-transparent', 'border-transparent');
            nameInput.classList.remove('border-gray-300', 'bg-gray-50');
            dangerZone.classList.add('hidden');
            btnSaveName.classList.add('hidden');
        }

        // 2. Lista de Membros
        const tbody = document.getElementById('membersListBody');
        tbody.innerHTML = '';

        membros.forEach(membro => {
            const isMe = membro.id == window.AppConfig.userId; // Precisa injetar userId no AppConfig

            // Badges
            let badges = '';
            if (membro.is_criador) {
                badges += `<span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded font-bold mr-1">Criador</span>`;
            } else if (membro.is_admin) {
                badges += `<span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold mr-1">Admin</span>`;
            } else {
                badges += `<span class="text-gray-500 text-xs px-2 py-1">Membro</span>`;
            }

            // Ações (Botões) - Só aparecem se EU for o CRIADOR e o alvo NÃO FOR O CRIADOR
            let actions = '';
            if (isCriador && !membro.is_criador) {
                // Botão Admin (Muda cor/icone baseado no estado atual)
                const adminIcon = membro.is_admin ? 'fa-user-shield text-blue-600' : 'fa-shield-alt text-gray-400';
                const adminTitle = membro.is_admin ? 'Remover Admin' : 'Tornar Admin';

                actions += `
                    <button onclick="ClassSettingsView.toggleAdmin(${membro.id})" class="mr-3 hover:bg-gray-100 p-1 rounded" title="${adminTitle}">
                        <i class="fas ${adminIcon}"></i>
                    </button>
                    <button onclick="ClassSettingsView.removeMember(${membro.id})" class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1 rounded" title="Remover usuário">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                `;
            }

            const tr = document.createElement('tr');
            tr.className = "border-b hover:bg-gray-50";
            tr.innerHTML = `
                <td class="p-3">
                    <div class="font-medium text-gray-800">${membro.nome} ${isMe ? '(Você)' : ''}</div>
                    <div class="text-xs text-gray-500">${membro.email}</div>
                </td>
                <td class="p-3">${badges}</td>
                <td class="p-3 text-right">${actions}</td>
            `;
            tbody.appendChild(tr);
        });
    },

    // No arquivo: js/class_settings_view.js

    syncUIAfterRename: function (novoNome) {
        // 1. Atualizar o nome da turma na tela de configurações (já deve estar certo)
        const settingsInput = document.getElementById('settingsClassName');
        if (settingsInput) settingsInput.value = novoNome;

        // 2. 🚨 ATUALIZAÇÃO NO HEADER (Badge Principal) 🚨
        const headerBadge = document.getElementById('header-class-name-badge');
        if (headerBadge) {
            // Atualiza apenas o texto do nome, mantendo "Turma: "
            headerBadge.textContent = "Turma: " + novoNome;
        }

        // 3. 🚨 ATUALIZAÇÃO NO HEADER (Dropdown Link) 🚨
        const headerDropdown = document.getElementById('header-class-name-dropdown');
        if (headerDropdown) {
            headerDropdown.textContent = novoNome;
        }
    },

    // --- AÇÕES ---

    updateName: async function () {
        const novoNome = document.getElementById('settingsClassName').value;
        const response = await fetch('renomear_turma.php', {
            method: 'POST',
            body: JSON.stringify({ turma_id: this.currentTurmaId, novo_nome: novoNome })
        });
        const data = await response.json();
        if (data.success) {
            alert('Nome atualizado!');
            document.getElementById('btnSaveName').classList.add('hidden');

            this.syncUIAfterRename(novoNome);
        } else {
            alert(data.error);
        }
    },

    toggleAdmin: async function (userId) {
        const response = await fetch('gerenciar_membro.php', {
            method: 'POST',
            body: JSON.stringify({
                turma_id: this.currentTurmaId,
                usuario_id: userId,
                acao: 'toggle_admin'
            })
        });
        const data = await response.json();
        if (data.success) this.loadData(); // Recarrega a lista para atualizar ícones
        else alert(data.error);
    },

    removeMember: async function (userId) {
        if (!confirm("Tem certeza que deseja remover este usuário da turma?")) return;

        const response = await fetch('gerenciar_membro.php', {
            method: 'POST',
            body: JSON.stringify({
                turma_id: this.currentTurmaId,
                usuario_id: userId,
                acao: 'remover'
            })
        });
        const data = await response.json();
        if (data.success) this.loadData();
        else alert(data.error);
    },

    deleteClass: async function () {
        const confirmacao = prompt("Para confirmar a exclusão, digite 'EXCLUIR':");
        if (confirmacao !== 'EXCLUIR') return alert("Ação cancelada.");

        const response = await fetch('excluir_turma.php', {
            method: 'POST',
            body: JSON.stringify({ turma_id: this.currentTurmaId })
        });
        const data = await response.json();

        if (data.success) {
            alert("Turma excluída.");
            window.location.reload(); // Recarrega app inteira
        } else {
            alert(data.error);
        }
    }

};