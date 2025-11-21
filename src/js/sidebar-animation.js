/**
 * sidebar-animation.js
 * Script global para o comportamento da Sidebar.
 * VERSÃO 2 (Mais Robusta)
 */

document.addEventListener('DOMContentLoaded', () => {

    // 1. Seleciona os elementos principais
    const body = document.querySelector('body');
    const sidebar = body.querySelector('.sidebar');
    const toggleOpen = body.querySelector('.toggle-open'); // Botão Hambúrguer

    // 2. MUDANÇA IMPORTANTE:
    // Procura o botão 'fechar' DENTRO da sidebar que já encontramos.
    // Se a sidebar não existir, 'toggleClose' será 'null' e o script não vai quebrar.
    const toggleClose = sidebar.querySelector('.toggle-close');

    // 3. Verifica se todos os elementos necessários existem
    if (sidebar && toggleOpen && toggleClose) {

        // 4. Ação: Abrir a sidebar
        toggleOpen.addEventListener("click", () => {
            sidebar.classList.remove("close");
        });

        // 5. Ação: Fechar a sidebar
        toggleClose.addEventListener("click", () => {
            sidebar.classList.add("close");
        });

        // 6. Comportamento Responsivo (Mobile)

        // Iniciar fechada em telas pequenas
        if (window.innerWidth < 768) {
            sidebar.classList.add("close");
        }

        // Fechar ao clicar fora (Apenas em Mobile)
        document.addEventListener('click', (e) => {
            // Verifica se a tela é mobile E a sidebar está aberta
            if (window.innerWidth < 768 && !sidebar.classList.contains('close')) {
                
                // Se o clique NÃO foi dentro da sidebar E NÃO foi no botão de abrir...
                if (!sidebar.contains(e.target) && !toggleOpen.contains(e.target)) {
                    // ...fecha a sidebar.
                    sidebar.classList.add("close");
                }
            }
        });
    
    } else {
        // Aviso para nós, desenvolvedores, caso algo esteja faltando na página
        console.warn("Script da Sidebar: Elementos essenciais (.sidebar, .toggle-open ou .toggle-close) não foram encontrados. A sidebar não funcionará.");
    }
});