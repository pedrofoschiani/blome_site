<?php
    // As variáveis $userName, $userAvatar e $role já vêm
    // dos scripts 'adminSection.php', 'professorSection.php', etc.
    $role = $_SESSION['user_role'] ?? 'user';
?>

<div class="profile-wrapper">

    <?php if (isset($_GET['success'])): ?>
        <p class="msg-box msg-success">Perfil atualizado com sucesso!</p>
    <?php elseif (isset($_GET['error'])): ?>
        <p class="msg-box msg-error">Ocorreu um erro: <?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>


    <div class="profile-card">
        <div class="profile-card__avatar">
            <img id="avatar-preview-image" src="<?php echo htmlspecialchars($userAvatar); ?>" alt="Avatar de <?php echo htmlspecialchars($userName); ?>">
        </div>
        <div class="profile-card__info">
            <h4><?php echo htmlspecialchars($userName); ?></h4>
            <p><?php echo ucfirst($role);?> da Instituição</p>
        </div>
    </div>


    <div class="profile-editor">
        
        <a id="toggle-profile-editor" class="button-toggle-edit">
            <i class='bx bx-edit'></i>
            <span>Editar Perfil (Nome, E-mail, Senha)</span>
            <i class='bx bx-chevron-down icon-toggle'></i>
        </a>
        
        <div id="profile-form-container" class="profile-form-wrapper">
            <form class="profile-form" action="../../../components/profile/profile-update.process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="full_name">Nome Completo</label>
                    <input type="text" id="full_name" name="full_name" 
                           class="line-input"
                           placeholder="<?php echo htmlspecialchars($userName); ?>">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label for="avatar_file">Alterar Foto de Perfil</label>
                    <input type="file" id="avatar_file" name="avatar_file" accept="image/png, image/jpeg" class="file-input">
                </div>

                <div class="form-group">
                    <label for="new_email">Alterar E-mail</label>
                    <input type="email" id="new_email" name="new_email" 
                           class="line-input"
                           placeholder="Digite o novo e-mail">
                    <small>Um e-mail de confirmação será enviado para o endereço antigo e o novo.</small>
                </div>

                <div class="form-group">
                    <label for="new_password">Alterar Senha</label>
                    <input type="password" id="new_password" name="new_password" autocomplete="new-password"
                            class="line-input"
                            placeholder="Digite a nova senha (mín. 8 caracteres)">
                            
                    <div class="password-requirements" id="profile-pass-reqs">
                        <div class="req-item req-length"><i class='bx bx-check'></i> Mínimo 8 caracteres</div>
                        <div class="req-item req-upper"><i class='bx bx-check'></i> Uma letra maiúscula</div>
                        <div class="req-item req-number"><i class='bx bx-check'></i> Um número</div>
                        <div class="req-item req-special"><i class='bx bx-check'></i> Um caractere especial</div>
                    </div>
                </div>

                <script src="../../../js/form-validator.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        setupFormValidation('new_email', 'new_password', 'profile-pass-reqs');
                    });
                </script>

                <div class="form-actions">
                    <button type="submit" class="button-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

</div>


<style>
    /* CSS para o container de perfil */
    .profile-wrapper { max-width: 700px; margin: 20px auto; }
    
    /* Card de Informações */
    .profile-card {
        background: #fff;
        border-radius: 35px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px; /* Espaço extra */
    }
    .profile-card__avatar img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 3px solid var(--primary-color);
        object-fit: cover;
        transition: all 0.3s ease;
    }
    .profile-card__info h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color-dark);
        margin: 0;
    }
    .profile-card__info p {
        font-size: 1rem;
        color: var(--text-color-muted);
        margin: 4px 0 0;
    }

    /* Editor Retrátil */
    .profile-editor {
        background: #fff;
        border-radius: 30px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        overflow: hidden;
    }
    .button-toggle-edit {
        padding: 20px 24px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--primary-color);
        transition: background-color 0.2s ease;
        text-decoration: none;
    }
    .button-toggle-edit:hover {
        background-color: #fdfaff;
    }
    .button-toggle-edit i { font-size: 1.3rem; }
    .button-toggle-edit .icon-toggle {
        font-size: 1.5rem;
        margin-left: auto;
        transition: transform 0.3s ease-out;
    }
    .button-toggle-edit.expanded .icon-toggle {
        transform: rotate(180deg);
    }

    /* O wrapper que será animado */
    .profile-form-wrapper {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.5s ease-out;
    }
    .profile-form-wrapper.expanded {
        max-height: 1200px; /* Aumentado para garantir que caiba tudo */
    }

    /* O formulário em si */
    .profile-form {
        padding: 30px 40px; /* Padding maior igual login */
        border-top: 1px solid #eee;
    }

    /* --- ESTILIZAÇÃO DOS INPUTS (IGUAL LOGIN) --- */
    .form-group { 
        margin-bottom: 30px; /* Espaçamento igual ao login */
        position: relative;
    }

    /* Label (Texto acima do input) */
    .form-group label { 
        display: block; 
        margin-bottom: 10px;
        font-weight: 700;
        font-size: 0.85rem;
        color: #555;
        text-transform: uppercase; /* Caixa alta igual login */
        letter-spacing: 0.5px;
    }

    /* Inputs de Texto (Linha apenas) */
    .line-input { 
        width: 100%; 
        padding: 10px 5px; 
        font-size: 1rem;
        line-height: 24px;
        color: #333;
        background-color: transparent;
        border: none;
        border-bottom: 2px solid #ddd; /* A linha */
        border-radius: 0;
        outline: none;
        transition: border-color 0.3s ease;
    }

    /* Foco no Input */
    .line-input:focus {
        border-bottom-color: var(--primary-color);
    }
    
    /* Placeholder */
    .line-input::placeholder {
        color: #bbb;
        font-weight: 400;
        font-size: 0.95rem;
    }

    /* Input de Arquivo (Mantido visual simples pois underline fica estranho) */
    .file-input {
        width: 100%;
        padding: 10px 0;
        font-size: 0.9rem;
    }

    /* Pequeno texto de ajuda */
    .form-group small { 
        font-size: 0.8rem; 
        color: #888; 
        margin-top: 8px;
        display: block;
    }

    /* Mensagens de Sucesso/Erro */
    .msg-box { padding: 15px; border-radius: 30px; margin-bottom: 25px; text-align: center; font-weight: 600; }
    .msg-success { background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .msg-error { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
    
    /* Botão */
    .form-actions {
        margin-top: 40px;
        text-align: center;
    }
    .button-primary { 
        background: var(--gradient);
        color: white;
        padding: 14px 30px;
        border: none;
        border-radius: 30px;
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--tran-03);
        width: 100%; /* Botão full width igual login, ou remova para botão menor */
        box-shadow: 0 4px 10px rgba(91, 52, 235, 0.2);
    }
    .button-primary:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Seleciona o botão e o container do formulário
    const toggleButton = document.getElementById('toggle-profile-editor');
    const formContainer = document.getElementById('profile-form-container');

    // Só executa se ambos existirem na página
    if (toggleButton && formContainer) {
        toggleButton.addEventListener('click', (e) => {
            e.preventDefault();
            toggleButton.classList.toggle('expanded');
            formContainer.classList.toggle('expanded');
        });
    }

    const fileInput = document.getElementById('avatar_file');
    const previewImage = document.getElementById('avatar-preview-image');

    if (fileInput && previewImage) {
        fileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

<script src="../../../js/message-handler.js"></script>