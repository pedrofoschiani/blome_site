<?php
    require_once '../adminSection.php';

    // 1. Buscas no Banco
    $professors = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_info?select=id,full_name", "GET", null, $_SESSION['access_token']);
    $subjects = \supabaseRestRequest($supabaseUrl, $supabaseKey, "subjects?select=*", "GET", null, $_SESSION['access_token']);
    $classes = \supabaseRestRequest($supabaseUrl, $supabaseKey, "classes?select=*", "GET", null, $_SESSION['access_token']);
    
    // 2. Relação Professor-Matéria
    $relations = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects?select=professor_id,subject_id", "GET", null, null);
    
    $profSubjectMap = [];
    if (!empty($relations) && !isset($relations['error'])) {
        foreach ($relations as $rel) {
            $profSubjectMap[$rel['professor_id']][] = $rel['subject_id'];
        }
    }
    
    // Mapas
    $profMap = []; foreach($professors as $p) $profMap[$p['id']] = $p['full_name'];
    $subMap = []; foreach($subjects as $s) $subMap[$s['id']] = $s['name'];
    $classMap = []; foreach($classes as $c) $classMap[$c['id']] = $c['class_name'];

    // Busca do Horário
    $selectedClassId = $_GET['class_filter'] ?? null;
    $scheduleData = [];
    if ($selectedClassId && $selectedClassId !== 'none') {
        $query = "professors_schedule?class_id=eq.$selectedClassId&order=day_of_week.asc,start_time.asc";
        $rawSchedule = \supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "GET", null, $_SESSION['access_token']);
        if (!empty($rawSchedule) && !isset($rawSchedule['error'])) {
            foreach($rawSchedule as $item) {
                $scheduleData[$item['day_of_week']][] = $item;
            }
        }
    }

    // Dias da Semana
    $weekDays = [
        1 => 'Segunda-feira',
        2 => 'Terça-feira',
        3 => 'Quarta-feira',
        4 => 'Quinta-feira',
        5 => 'Sexta-feira',
        6 => 'Sábado',
        0 => 'Domingo'
    ];
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Calendário</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="admins-calendary.page.css">
    </head>
    <body>
        <?php include '../../../components/sidebars/admin-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-prof">
                <?php if(isset($_GET['success'])): ?><div class="msg-box msg-success">Sucesso!</div><?php endif; ?>
                <?php if(isset($_GET['error'])): ?><div class="msg-box msg-error"><?php echo htmlspecialchars($_GET['error']); ?></div><?php endif; ?>

                <div class="filter-section">
                    <label class="filter-label"><i class='bx bx-filter-alt'></i> Selecione a Turma:</label>
                    <form action="" method="GET" style="flex: 1; max-width: 400px;">
                        <div class="input-group" style="margin-bottom: 0;">
                            <select name="class_filter" onchange="this.form.submit()">
                                <option value="none" disabled <?php echo !$selectedClassId ? 'selected' : ''; ?>>-- Escolha uma sala --</option>
                                <?php foreach($classes as $cls): ?>
                                    <option value="<?php echo $cls['id']; ?>" <?php echo $selectedClassId == $cls['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cls['class_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>

                <?php if($selectedClassId): ?>
                    <div class="calendar-container">
                        <button class="nav-arrow nav-prev" id="prevBtn"><i class='bx bx-chevron-left'></i></button>
                        <div class="calendar-wrapper" id="calendarWrapper">
                            <?php foreach($weekDays as $dayNum => $dayName): ?>
                                <div class="day-column">
                                    <div class="day-header"><?php echo $dayName; ?></div>
                                    <?php 
                                        $lastEndTime = "07:00:00"; 
                                        if(isset($scheduleData[$dayNum])): 
                                            foreach($scheduleData[$dayNum] as $lesson):
                                                $profName = $profMap[$lesson['professor_id']] ?? 'Desconhecido';
                                                $subName = $subMap[$lesson['subject_id']] ?? 'Matéria removida';
                                                $lessonData = htmlspecialchars(json_encode($lesson), ENT_QUOTES, 'UTF-8');
                                                $lastEndTime = $lesson['end_time'];
                                    ?>
                                        <div class="lesson-card" data-lesson="<?php echo $lessonData; ?>" onclick="openEditModal(this)">
                                            <div class="lesson-time"><i class='bx bx-time-five'></i> <?php echo substr($lesson['start_time'], 0, 5) . ' - ' . substr($lesson['end_time'], 0, 5); ?></div>
                                            <div class="lesson-subject"><?php echo htmlspecialchars($subName); ?></div>
                                            <div class="lesson-prof"><?php echo htmlspecialchars($profName); ?></div>
                                        </div>
                                    <?php endforeach; endif; ?>
                                    <div class="btn-add-lesson" onclick="openAddModal('<?php echo $dayNum; ?>', '<?php echo $lastEndTime; ?>')"><i class='bx bx-plus'></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="nav-arrow nav-next" id="nextBtn"><i class='bx bx-chevron-right'></i></button>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <div class="modal-overlay" id="scheduleModal">
            <div class="modal-card">
                <div class="modal-header">
                    <h3 id="modalTitle">Adicionar Aula</h3>
                    <i class='bx bx-x close-modal' onclick="closeModal()"></i>
                </div>
                
                <form action="manage-calendary.process.php" method="POST" id="scheduleForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="create">

                    <input type="hidden" name="action" id="formAction" value="create">
                    <input type="hidden" name="class_id" value="<?php echo $selectedClassId; ?>">
                    <input type="hidden" name="schedule_id" id="scheduleId">
                    <input type="hidden" name="day_of_week" id="dayOfWeekInput">

                    <div class="form-row">
                        <div class="input-group">
                            <label>Início</label>
                            <input type="time" name="start_time" id="startTime" required>
                        </div>
                        <div class="input-group">
                            <label>Término</label>
                            <input type="time" name="end_time" id="endTime" required>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Professor</label>
                        <select name="professor_id" id="profSelect" required onchange="updateSubjects()">
                            <option value="">Selecione um professor...</option>
                            <?php foreach($professors as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label>Matéria</label>
                        <select name="subject_id" id="subjectSelect" required disabled>
                            <option value="">Primeiro selecione o professor</option>
                        </select>
                    </div>

                    <div class="modal-actions">
                        <button type="button" id="btnDelete" class="btn-icon-delete" style="display:none;" onclick="deleteSchedule()" title="Excluir Aula">
                            <i class='bx bx-trash'></i>
                        </button>
                        <button type="submit" class="btn-save-modal">Salvar Aula</button>
                    </div>
                </form>
                
                <form id="deleteForm" action="manage-calendary.process.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="schedule_id" id="deleteId">
                    <input type="hidden" name="class_id" value="<?php echo $selectedClassId; ?>">
                </form>
            </div>
        </div>

        <script src="../../../js/sidebar-animation.js"></script>
        <script src="../../../js/message-handler.js"></script>
        
        <script>
            const allSubjects = <?php echo json_encode($subjects); ?>;
            const profSubjectMap = <?php echo json_encode($profSubjectMap); ?>;
        </script>
        
        <script src="admins-calendary.page.js"></script>
    </body>
</html>