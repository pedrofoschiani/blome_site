/**
 * Script global de validação de formulários.
 * Deve ser importado antes do script da página.
 */
function setupFormValidation(emailId, passwordId, requirementsId) {
    const emailInput = document.getElementById(emailId);
    const passwordInput = document.getElementById(passwordId);
    const requirementsBox = document.getElementById(requirementsId);

    // Se não achar os elementos, para (evita erros no console)
    if (!passwordInput || !requirementsBox) return;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const hasUpper = /[A-Z]/;
    const hasNumber = /[0-9]/;
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/;

    // --- Validação de Email ---
    if (emailInput) {
        emailInput.addEventListener('input', () => {
            if (emailRegex.test(emailInput.value)) {
                emailInput.classList.remove('invalid');
                emailInput.classList.add('valid');
            } else {
                if(emailInput.value === "") {
                    emailInput.classList.remove('invalid', 'valid');
                } else {
                    emailInput.classList.remove('valid');
                }
            }
        });

        emailInput.addEventListener('blur', () => {
            if (!emailRegex.test(emailInput.value) && emailInput.value !== "") {
                emailInput.classList.add('invalid');
            }
        });
    }

    // --- Validação de Senha ---
    
    // Focou no input: Mostra a caixa
    passwordInput.addEventListener('focus', () => {
        requirementsBox.classList.add('show');
    });

    // Saiu do input: Esconde (se estiver vazio)
    passwordInput.addEventListener('blur', () => {
        if(passwordInput.value === "") {
             requirementsBox.classList.remove('show');
        }
    });

    // Digitou: Valida em tempo real
    passwordInput.addEventListener('input', () => {
        const val = passwordInput.value;
        
        // Garante que apareça ao digitar
        if(val.length > 0) requirementsBox.classList.add('show');

        const updateReq = (selector, isValid) => {
            const el = requirementsBox.querySelector(selector); 
            if(el) {
                const icon = el.querySelector('i');
                if (isValid) {
                    el.classList.remove('unmet');
                    el.classList.add('met');
                    if(icon) { icon.classList.remove('bx-check', 'bx-circle'); icon.classList.add('bx-check-circle'); }
                } else {
                    el.classList.remove('met');
                    el.classList.add('unmet');
                    if(icon) { icon.classList.remove('bx-check-circle'); icon.classList.add('bx-check'); } // Volta ao icone original
                }
            }
        };

        updateReq('.req-length', val.length >= 8);
        updateReq('.req-upper', hasUpper.test(val));
        updateReq('.req-number', hasNumber.test(val));
        updateReq('.req-special', hasSpecial.test(val));

        if (val.length >= 8 && hasUpper.test(val) && hasNumber.test(val) && hasSpecial.test(val)) {
            passwordInput.classList.remove('invalid');
            passwordInput.classList.add('valid');
        } else {
            passwordInput.classList.remove('valid');
        }
    });
}