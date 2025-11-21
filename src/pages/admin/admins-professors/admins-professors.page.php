<?php
    require_once '../adminSection.php';
    // Mantendo a lógica original
    $professors = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_info?select=*", "GET", null, $_SESSION['access_token']);
    $allSubjects = \supabaseRestRequest($supabaseUrl, $supabaseKey, "subjects?select=*", "GET", null, $_SESSION['access_token']);
    $relations = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_subjects?select=professor_id,subject_id", "GET", null, $_SESSION['access_token']);

    $profSubjectsMap = [];
    if (!empty($relations) && !isset($relations['error'])) {
        foreach ($relations as $rel) {
            $profSubjectsMap[$rel['professor_id']][] = $rel['subject_id'];
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE-edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Admin</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../admin-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="admins-professors.page.css">
    </head>
    <body>
    
        <?php include '../../../components/sidebars/admin-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-prof">
            
                <?php if(isset($_GET['success'])): ?>
                    <div class="msg-box msg-success">Operação realizada com sucesso!</div>
                <?php elseif(isset($_GET['error'])): ?>
                    <div class="msg-box msg-error">Erro: <?php echo htmlspecialchars($_GET['error']); ?></div>
                <?php endif; ?>
                
                <div class="row-cards">
                    <div class="card-form">
                        <h3>Adicionar Novo Professor</h3>
                        <form action="manage-professors.process.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="prof_name">Nome Completo</label>
                                    <input type="text" id="prof_name" name="name" placeholder="Ex: Professor Escola" required>
                                </div>
                                <div class="input-group">
                                    <label for="prof_email">E-mail de Acesso</label>
                                    <input type="email" id="prof_email" name="email" placeholder="Ex: professor@escola.com" required>
                                </div>
                                <div class="input-group" style="position: relative;">
                                    <label for="prof_pass">Senha Inicial</label>
                                    <input type="password" id="prof_pass" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
                                    
                                    <div class="password-requirements reqs-floating" id="prof-pass-reqs">
                                        <div class="req-item req-length"><i class='bx bx-check'></i> Mínimo 8 caracteres</div>
                                        <div class="req-item req-upper"><i class='bx bx-check'></i> Uma letra maiúscula</div>
                                        <div class="req-item req-number"><i class='bx bx-check'></i> Um número</div>
                                        <div class="req-item req-special"><i class='bx bx-check'></i> Um caractere especial</div>
                                    </div>
                                </div>
                                <script src="../../../js/form-validator.js"></script> 
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        setupFormValidation('prof_email', 'prof_pass', 'prof-pass-reqs');
                                    });
                                </script>
                            </div>
                            <div style="text-align: right;">
                                <button type="submit" class="btn-add">
                                    <i class='bx bx-plus'></i> Cadastrar Professor
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-form">
                        <h3>Adicionar Matéria</h3>
                        <form action="manage-professors.process.php" method="POST">
                            <input type="hidden" name="action" value="create_subject">
                            
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">
                                Cadastre as disciplinas oferecidas pela escola para vincular aos professores depois.
                            </p>

                            <div class="input-group" style="margin-bottom: 25px;">
                                <input type="text" name="subject_name" placeholder="Nome da Matéria" required>
                            </div>
                            
                            <div style="text-align: right; margin-top: auto;">
                                <button type="submit" class="btn-add" style="width: 100%; justify-content: center;">
                                    <i class='bx bx-book-add'></i> Criar Matéria
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 40px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <table class="table-list">
                        <thead>
                            <tr>
                                <th>Nome da Matéria</th>
                                <th style="width: 100px; text-align: center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($allSubjects) && !isset($allSubjects['error'])): ?>
                                <?php foreach($allSubjects as $sub): ?>
                                <tr>
                                    <td>
                                        <span style="font-weight: 700; color: var(--text-color-dark);"><?php echo htmlspecialchars($sub['name']); ?></span>
                                    </td>
                                    
                                    <td style="display: flex; justify-content: center; align-items: center;">
                                        <form action="manage-professors.process.php" method="POST" onsubmit="return confirm('Ao deletar esta matéria, ela será removida de todos os professores que a lecionam. Continuar?');">
                                            <input type="hidden" name="action" value="delete_subject">
                                            <input type="hidden" name="subject_id" value="<?php echo $sub['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete" title="Excluir Matéria">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" style="text-align:center; padding: 20px;">Nenhuma matéria cadastrada.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive-wrapper">
                    <table class="table-list">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Avatar</th>
                                <th>Nome</th>
                                <th style="width: auto; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($professors) && !isset($professors['error'])): ?>
                                <?php foreach($professors as $prof): ?>
                                    <?php 
                                        $mySubjects = $profSubjectsMap[$prof['id']] ?? [];
                                        $jsonSubjects = json_encode($mySubjects);
                                    ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($prof['avatar_url'] ?? 'https://placehold.co/150'); ?>" 
                                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($prof['full_name']); ?></td>
                                    <td style="text-align: center; display: flex; justify-content: center; gap: 10px;">
                                        <button type="button" class="btn-action btn-subjects" 
                                                onclick='openModal("<?php echo $prof['id']; ?>", "<?php echo $prof['full_name']; ?>", <?php echo $jsonSubjects; ?>)'
                                                title="Gerenciar Matérias">
                                                <i class='bx bx-book-bookmark'></i>
                                        </button>
                                        <form action="manage-professors.process.php" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este professor? O acesso dele será revogado.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $prof['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete" title="Remover">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align:center; padding: 30px;">Nenhum professor encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <div class="modal-overlay" id="subjectModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 id="modalTitle">Matérias</h3>
                    <i class='bx bx-x close-modal' onclick="closeModal()"></i>
                </div>
                
                <form action="manage-professors.process.php" method="POST">
                    <input type="hidden" name="action" value="update_subjects">
                    <input type="hidden" name="professor_id" id="modalProfId">
                    
                    <div class="subjects-grid">
                        <?php if(!empty($allSubjects) && !isset($allSubjects['error'])): ?>
                            <?php foreach($allSubjects as $sub): ?>
                                <label class="subject-option">
                                    <input type="checkbox" class="custom-checkbox-input" name="subjects[]" value="<?php echo $sub['id']; ?>" id="sub_<?php echo $sub['id']; ?>">
                                    <span><?php echo htmlspecialchars($sub['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p style="color: #666; text-align: center; grid-column: span 2;">Nenhuma matéria cadastrada no sistema.</p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-save-modal"><p>Salvar Alterações</p></button>
                </form>
            </div>
        </div>
        
        <script src="../../../js/sidebar-animation.js"></script>
        <script src="../../../js/message-handler.js"></script>

        <script>
            const modal = document.getElementById('subjectModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalProfId = document.getElementById('modalProfId');
            const checkboxes = document.querySelectorAll('input[name="subjects[]"]');

            function openModal(profId, profName, currentSubjects) {
                modalTitle.innerText = "Matérias: " + profName;
                modalProfId.value = profId;
                checkboxes.forEach(cb => cb.checked = false);
                
                if(currentSubjects) {
                    currentSubjects.forEach(subId => {
                        const check = document.getElementById('sub_' + subId);
                        if(check) check.checked = true;
                    });
                }
                modal.classList.add('open');
            }

            function closeModal() {
                modal.classList.remove('open');
            }

            modal.addEventListener('click', (e) => {
                if(e.target === modal) closeModal();
            });
        </script>
    </body>
</html>