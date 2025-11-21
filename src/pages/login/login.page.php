<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE-edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

        <link rel="stylesheet" href="../../style/global.css">
        <link rel="stylesheet" href="../../style/variable.css">
        <link rel="stylesheet" href="login.page.css">
        <title>BLOME | Entrar</title>
    </head>
    <body>

        <div class="login-header">
            <img src="../../assets/logo/logoB_A.png" alt="Logo BLOME" class="login-logo">
            <h1 class="login-title">Acesse sua conta</h1>
        </div>

        <div class="login-box">
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        switch($_GET['error']) {
                            case 'invalid_credentials': echo "E-mail ou senha incorretos."; break;
                            case 'no_profile': echo "Usuário sem perfil associado."; break;
                            case 'unauthorized': echo "Acesso não autorizado."; break;
                            case 'not_logged_in': echo "Faça login para continuar."; break;
                            default: echo "Ocorreu um erro. Tente novamente.";
                        }
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <?php if($_GET['success'] == 'auth_updated') echo "Senha alterada! Faça login novamente."; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.process.php" id="loginForm">
                
                <!-- Input E-mail -->
                <div class="form-group">
                    <label class="form-label" for="email">Endereço de e-mail</label>
                    <input type="email" id="email" name="email" 
                           class="form-input" 
                           required autofocus 
                           placeholder="ex: aluno@escola.com">
                </div>
                
                <!-- Input Senha -->
                <div class="form-group">
                    <label class="form-label" for="password">Senha</label>
                    <input type="password" id="password" name="password" 
                           class="form-input" 
                           required
                           placeholder="Digite sua senha">
                    
                    <!-- Lista de validação em tempo real -->
                    <div class="password-requirements" id="passwordRequirements">
                        <div class="req-item" id="req-length"><i class='bx bx-check'></i> Mínimo 8 caracteres</div>
                        <div class="req-item" id="req-upper"><i class='bx bx-check'></i> Uma letra maiúscula</div>
                        <div class="req-item" id="req-number"><i class='bx bx-check'></i> Um número</div>
                        <div class="req-item" id="req-special"><i class='bx bx-check'></i> Um caractere especial (@, #, etc)</div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit" id="submitBtn">Entrar</button>
            </form>
        </div>

        <a href="../landing/landing.page.html" class="back-home">
            <i class='bx bx-arrow-back'></i> Voltar para o início
        </a>

        <!-- Scripts de Validação -->
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                const requirementsBox = document.getElementById('passwordRequirements');
                
                // Regex para validação de Email Simples
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                // Regex para partes da senha
                const hasUpper = /[A-Z]/;
                const hasNumber = /[0-9]/;
                const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/;
                
                // --- Validação de Email ---
                emailInput.addEventListener('input', () => {
                    if (emailRegex.test(emailInput.value)) {
                        emailInput.classList.remove('invalid');
                        emailInput.classList.add('valid');
                    } else {
                        if(emailInput.value === "") {
                            emailInput.classList.remove('invalid', 'valid');
                        } else {
                            emailInput.classList.remove('valid');
                            // Opcional: só marca inválido no blur para não irritar enquanto digita
                            // emailInput.classList.add('invalid'); 
                        }
                    }
                });

                emailInput.addEventListener('blur', () => {
                     if (!emailRegex.test(emailInput.value) && emailInput.value !== "") {
                         emailInput.classList.add('invalid');
                     }
                });

                // --- Validação de Senha ---
                passwordInput.addEventListener('focus', () => {
                    requirementsBox.classList.add('show');
                });

                // Opcional: Esconder ao sair se estiver tudo certo, ou manter para feedback
                // passwordInput.addEventListener('blur', () => { ... });

                passwordInput.addEventListener('input', () => {
                    const val = passwordInput.value;

                    // 1. Verifica tamanho (8 chars)
                    updateRequirement('req-length', val.length >= 8);

                    // 2. Verifica Maiúscula
                    updateRequirement('req-upper', hasUpper.test(val));

                    // 3. Verifica Número
                    updateRequirement('req-number', hasNumber.test(val));

                    // 4. Verifica Especial
                    updateRequirement('req-special', hasSpecial.test(val));

                    // Validação visual do input inteiro
                    if (val.length >= 8 && hasUpper.test(val) && hasNumber.test(val) && hasSpecial.test(val)) {
                        passwordInput.classList.remove('invalid');
                        passwordInput.classList.add('valid');
                    } else {
                        passwordInput.classList.remove('valid');
                        // passwordInput.classList.add('invalid'); // Opcional durante a digitação
                    }
                });

                function updateRequirement(id, isValid) {
                    const el = document.getElementById(id);
                    if (isValid) {
                        el.classList.remove('unmet');
                        el.classList.add('met');
                        el.querySelector('i').classList.replace('bx-circle', 'bx-check-circle'); // Se usar ícones diferentes
                    } else {
                        el.classList.remove('met');
                        el.classList.add('unmet');
                    }
                }
            });
        </script>
    </body>
</html>