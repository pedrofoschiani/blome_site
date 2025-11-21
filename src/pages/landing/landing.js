// Adicione este script no final do seu <body> ou no seu arquivo landing.js

document.addEventListener("DOMContentLoaded", () => {
    
    // --- 1. Funcionalidade das ABAS (Categorias) ---
    const categoryButtons = document.querySelectorAll('.faq-category-btn');
    const categoryContents = document.querySelectorAll('.faq-category-content');

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');

            // Remove 'active' de todos os botões e conteúdos
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            categoryContents.forEach(content => content.classList.remove('active'));

            // Adiciona 'active' ao botão clicado
            button.classList.add('active');
            
            // Adiciona 'active' ao conteúdo correspondente
            document.getElementById(tabId).classList.add('active');
        });
    });


    // --- 2. Funcionalidade do ACORDEÃO (Perguntas) ---
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const item = question.closest('.faq-item');
            closeAllOtherItems(item);
            item.classList.toggle('active');
        });
    });

    function closeAllOtherItems(currentItem) {
            const allActiveItems = document.querySelectorAll('.faq-item.active');
            allActiveItems.forEach(activeItem => {
                if (activeItem !== currentItem) {
                    activeItem.classList.remove('active');
                }
            });
        }
});