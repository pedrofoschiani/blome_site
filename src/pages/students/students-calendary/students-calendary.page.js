document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('calendarWrapper');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    // Só roda o script se os elementos existirem (caso o aluno não tenha turma, o wrapper não existe)
    if (wrapper && prevBtn && nextBtn) {
        const scrollAmount = 300; 

        const updateArrows = () => {
            const maxScroll = wrapper.scrollWidth - wrapper.clientWidth;
            prevBtn.classList.toggle('hidden', wrapper.scrollLeft <= 5);
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
        updateArrows();
        window.addEventListener('resize', updateArrows);
    }
});