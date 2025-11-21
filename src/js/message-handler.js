document.addEventListener('DOMContentLoaded', () => {
    // Seleciona todas as mensagens de feedback na tela
    const messages = document.querySelectorAll('.msg-box');

    if (messages.length > 0) {
        // Espera 5000ms (5 segundos)
        setTimeout(() => {
            messages.forEach(msg => {
                // Adiciona uma transição suave via CSS inline ou classe
                msg.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                msg.style.opacity = "0";
                msg.style.transform = "translateY(-20px)"; // Sobe um pouco ao sumir

                // Remove o elemento do HTML depois que a transição visual acabar (500ms)
                setTimeout(() => {
                    msg.remove();
                }, 500); 
            });
        }, 5000);
    }
});