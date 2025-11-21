// --- Script do Carrossel ---
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('calendarWrapper');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    if (wrapper && prevBtn && nextBtn) {
        const scrollAmount = 300; // Quantidade de scroll por clique

        const updateArrows = () => {
            const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
            // Esconde a seta esquerda se estiver no começo
            prevBtn.classList.toggle('hidden', wrapper.scrollLeft <= 5);
            // Esconde a seta direita se estiver no fim
            nextBtn.classList.toggle('hidden', wrapper.scrollLeft >= maxScroll - 5);
        };

        prevBtn.addEventListener('click', () => { 
            wrapper.scrollBy({ left: -scrollAmount, behavior: 'smooth' }); 
            setTimeout(updateArrows, 500); 
        });

        nextBtn.addEventListener('click', () => { 
            wrapper.scrollBy({ left: scrollAmount, behavior: 'smooth' }); 
            setTimeout(updateArrows, 500); 
        });

        wrapper.addEventListener('scroll', updateArrows);
        
        // Inicializa o estado das setas e ajusta ao redimensionar tela
        updateArrows();
        window.addEventListener('resize', updateArrows);
    }
});