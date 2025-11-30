const ViewManager = {
    switch: (viewId) => {
        // Esconde todas as seções
        document.querySelectorAll('.view-section').forEach(view => {
            view.classList.add('hidden');
        });

        // Mostra a desejada
        const target = document.getElementById(viewId);
        if (target) {
            target.classList.remove('hidden');
            window.scrollTo(0, 0);
        } else {
            console.error(`View ${viewId} não encontrada`);
        }
        
        // Esconde menu de usuário se estiver aberto (UX)
        const userMenu = document.getElementById('userMenu');
        if(userMenu) userMenu.classList.add('hidden');
    }
};