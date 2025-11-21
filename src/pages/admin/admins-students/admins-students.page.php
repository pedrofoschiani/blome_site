<?php
    require_once '../adminSection.php';
    
    // 1. Busca Alunos (Sem o join complexo que estava quebrando)
    $students = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=*", "GET", null, $_SESSION['access_token']);
    
    // 2. Busca Salas
    $classes = \supabaseRestRequest($supabaseUrl, $supabaseKey, "classes?select=*", "GET", null, $_SESSION['access_token']);

    // 3. Cria um Mapa de Salas para facilitar o uso (ID => Nome)
    // Isso substitui o Join do banco de dados
    $classMap = [];
    if (!empty($classes) && !isset($classes['error'])) {
        foreach ($classes as $cls) {
            $classMap[$cls['id']] = $cls['class_name'];
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
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="admins-students.page.css">
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
                    <div class="msg-box msg-error">
                        Erro: <?php echo htmlspecialchars($_GET['error']); ?>
                        <?php if(isset($_GET['msg'])) echo " (" . htmlspecialchars($_GET['msg']) . ")"; ?>
                    </div>
                <?php endif; ?>
                
                <div class="row-cards">
                    
                    <div class="card-form">
                        <h3>Adicionar Novo Aluno</h3>
                        <form action="manage-students.process.php" method="POST">
                            <input type="hidden" name="action" value="create">
                            <div class="form-row">
                                <div class="input-group">
                                    <label for="stud_name">Nome Completo</label>
                                    <input type="text" id="stud_name" name="name" placeholder="Ex: Aluno Escola" required>
                                </div>
                                <div class="input-group">
                                    <label for="stud_email">E-mail</label>
                                    <input type="email" id="stud_email" name="email" placeholder="Ex: aluno@escola.com" required>
                                </div>
                                <div class="input-group" style="position: relative;">
                                    <label for="stud_pass">Senha Inicial</label>
                                    <input type="password" id="stud_pass" name="password" placeholder="Mínimo 8 caracteres" required minlength="8">
                                    
                                    <div class="password-requirements reqs-floating" id="stud-pass-reqs">
                                        <div class="req-item req-length"><i class='bx bx-check'></i> Mínimo 8 caracteres</div>
                                        <div class="req-item req-upper"><i class='bx bx-check'></i> Uma letra maiúscula</div>
                                        <div class="req-item req-number"><i class='bx bx-check'></i> Um número</div>
                                        <div class="req-item req-special"><i class='bx bx-check'></i> Um caractere especial</div>
                                    </div>
                                </div>
                                <script src="../../../js/form-validator.js"></script> 
                                <script>
                                    document.addEventListener('DOMContentLoaded', () => {
                                        setupFormValidation('stud_email', 'stud_pass', 'stud-pass-reqs');
                                    });
                                </script>
                            </div>
                            <div style="text-align: right;">
                                <button type="submit" class="btn-add">
                                    <i class='bx bx-plus'></i> Cadastrar Aluno
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="card-form">
                        <h3>Adicionar Sala de Aula</h3>
                        <form action="manage-students.process.php" method="POST">
                            <input type="hidden" name="action" value="create_class">
                            
                            <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">
                                Crie turmas para organizar seus alunos (Ex: 1º Ano A, 2º Ano B).
                            </p>

                            <div class="input-group" style="margin-bottom: 25px;">
                                <input type="text" name="class_name" placeholder="Nome da Sala (ex: 3º Ano C)" required>
                            </div>
                            
                            <div style="text-align: right; margin-top: auto;">
                                <button type="submit" class="btn-add" style="width: 100%; justify-content: center;">
                                    <i class='bx bx-building'></i> Criar Sala
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 40px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <table class="table-list">
                        <thead>
                            <tr>
                                <th>Salas Cadastradas</th>
                                <th style="width: 100px; text-align: center;">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($classes) && !isset($classes['error'])): ?>
                                <?php foreach($classes as $cls): ?>
                                <tr>
                                    <td>
                                        <span style="font-weight: 700; color: var(--text-color-dark);"><?php echo htmlspecialchars($cls['class_name']); ?></span>
                                    </td>
                                    <td style="display: flex; justify-content: center; align-items: center;">
                                        <form action="manage-students.process.php" method="POST" onsubmit="return confirm('Deseja excluir esta sala? Os alunos vinculados ficarão sem sala.');">
                                            <input type="hidden" name="action" value="delete_class">
                                            <input type="hidden" name="class_id" value="<?php echo $cls['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete" title="Excluir Sala">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="2" style="text-align:center; padding: 20px;">Nenhuma sala cadastrada.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="filter-section">
                    <label for="classFilter" style="font-weight: 700; color: var(--text-color-dark);">Filtrar por Sala:</label>
                    <div class="input-group" style="max-width: 300px;">
                        <select id="classFilter" onchange="filterStudents()">
                            <option value="all">Todas as Salas</option>
                            <option value="none">Sem Sala</option>
                            <?php if(!empty($classes) && !isset($classes['error'])): ?>
                                <?php foreach($classes as $cls): ?>
                                    <option value="<?php echo $cls['id']; ?>"><?php echo htmlspecialchars($cls['class_name']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="table-responsive-wrapper">
                    <table class="table-list" id="studentsTable">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Avatar</th>
                                <th>Nome</th>
                                <th>Sala</th>
                                <th style="width: auto; text-align: center;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($students) && !isset($students['error'])): ?>
                                <?php foreach($students as $stud): ?>
                                    <?php 
                                        // Mapeia o ID da sala para o Nome usando o array PHP que criamos lá em cima
                                        $currentClassId = $stud['class_id'] ?? 'none';
                                        $className = isset($classMap[$currentClassId]) ? $classMap[$currentClassId] : 'Sem Sala';
                                        
                                        // Se o ID for nulo, usamos 'none' para o filtro funcionar
                                        $filterId = $currentClassId ?: 'none';
                                    ?>
                                <tr class="student-row" data-class-id="<?php echo $filterId; ?>">
                                    <td>
                                        <img src="<?php echo htmlspecialchars($stud['avatar_url'] ?? 'https://placehold.co/150'); ?>" 
                                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($stud['full_name']); ?></td>
                                    <td>
                                        <span style="background: #f0f0f0; padding: 5px 10px; border-radius: 10px; font-size: 0.85rem; font-weight: 600; color: #666;">
                                            <?php echo htmlspecialchars($className); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center; display: flex; justify-content: center; gap: 10px;">
                                        
                                        <button type="button" class="btn-action btn-subjects" 
                                                onclick='openClassModal("<?php echo $stud['id']; ?>", "<?php echo $stud['full_name']; ?>", "<?php echo $filterId; ?>")'
                                                title="Mudar Sala">
                                                <i class='bx bx-building'></i>
                                        </button>

                                        <form action="manage-students.process.php" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este aluno?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $stud['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete" title="Remover">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center; padding: 30px;">Nenhum aluno encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </section>
        
        <div class="modal-overlay" id="classModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 id="modalTitle">Mudar Sala</h3>
                    <i class='bx bx-x close-modal' onclick="closeModal()"></i>
                </div>
                
                <form action="manage-students.process.php" method="POST">
                    <input type="hidden" name="action" value="update_student_class">
                    <input type="hidden" name="student_id" id="modalStudentId">
                    
                    <div class="subjects-grid">
                        <label class="subject-option">
                            <input type="radio" class="custom-radio-input" name="class_id" value="" id="cls_none">
                            <span>Sem Sala</span>
                        </label>

                        <?php if(!empty($classes) && !isset($classes['error'])): ?>
                            <?php foreach($classes as $cls): ?>
                                <label class="subject-option">
                                    <input type="radio" class="custom-radio-input" name="class_id" value="<?php echo $cls['id']; ?>" id="cls_<?php echo $cls['id']; ?>">
                                    <span><?php echo htmlspecialchars($cls['class_name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn-save-modal"><p>Salvar Alterações</p></button>
                </form>
            </div>
        </div>

        <script src="../../../js/sidebar-animation.js"></script>
        <script src="../../../js/message-handler.js"></script>

        <script>
            // --- Lógica do Modal ---
            const modal = document.getElementById('classModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalStudentId = document.getElementById('modalStudentId');
            const radios = document.querySelectorAll('input[name="class_id"]');

            function openClassModal(studId, studName, currentClassId) {
                modalTitle.innerText = "Sala: " + studName;
                modalStudentId.value = studId;
                
                // Limpa seleção anterior
                radios.forEach(r => r.checked = false);
                
                // Marca a sala atual
                if(currentClassId && currentClassId !== 'none') {
                    const check = document.getElementById('cls_' + currentClassId);
                    if(check) check.checked = true;
                } else {
                    const checkNone = document.getElementById('cls_none');
                    if(checkNone) checkNone.checked = true;
                }

                modal.classList.add('open');
            }

            function closeModal() {
                modal.classList.remove('open');
            }

            modal.addEventListener('click', (e) => {
                if(e.target === modal) closeModal();
            });

            // --- Lógica do Filtro (JavaScript Puro) ---
            function filterStudents() {
                const filterValue = document.getElementById('classFilter').value;
                const rows = document.querySelectorAll('.student-row');

                rows.forEach(row => {
                    const rowClassId = row.getAttribute('data-class-id');

                    if (filterValue === 'all') {
                        row.style.display = ''; // Mostra tudo
                    } else if (filterValue === 'none') {
                         // Mostra se classId for 'none' ou vazio
                        if(rowClassId === 'none' || rowClassId === '') {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Filtro Específico: tem que ser igual (lembrando que DB retorna int, html string)
                        if (rowClassId == filterValue) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            }
        </script>
    </body>
</html>