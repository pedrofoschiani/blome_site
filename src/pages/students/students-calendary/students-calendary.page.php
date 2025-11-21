<?php
    require_once '../studentSection.php';

    $studentId = $_SESSION['user_id'];

    // 1. Buscar dados extras do aluno para descobrir a TURMA (class_id)
    // O UserProfileLoader padrão traz apenas nome e avatar, então fazemos uma query extra rápida.
    $studentInfo = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=class_id&id=eq.$studentId", "GET", null, $_SESSION['access_token']);
    
    $myClassId = null;
    if (!empty($studentInfo) && !isset($studentInfo['error']) && isset($studentInfo[0]['class_id'])) {
        $myClassId = $studentInfo[0]['class_id'];
    }

    // 2. Se o aluno tem turma, buscamos o horário
    $scheduleData = [];
    $profMap = [];
    $subMap = [];

    if ($myClassId) {
        // Busca Horários da Turma
        $query = "professors_schedule?class_id=eq.$myClassId&order=day_of_week.asc,start_time.asc";
        $rawSchedule = \supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "GET", null, $_SESSION['access_token']);

        // Busca Professores (para saber o nome)
        $professors = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_info?select=id,full_name", "GET", null, $_SESSION['access_token']);
        
        // Busca Matérias
        $subjects = \supabaseRestRequest($supabaseUrl, $supabaseKey, "subjects?select=*", "GET", null, $_SESSION['access_token']);

        // Mapeando para acesso rápido
        if (!empty($professors) && !isset($professors['error'])) {
            foreach($professors as $p) $profMap[$p['id']] = $p['full_name'];
        }
        if (!empty($subjects) && !isset($subjects['error'])) {
            foreach($subjects as $s) $subMap[$s['id']] = $s['name'];
        }

        // Organizando por dia
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
        <title>BLOME | Minhas Aulas</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../students-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="students-calendary.page.css">
    </head>
    <body>
        <?php include '../../../components/sidebars/student-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-prof">

                <?php if($myClassId): ?>
                    <div class="calendar-container">
                        <button class="nav-arrow nav-prev" id="prevBtn"><i class='bx bx-chevron-left'></i></button>
                        
                        <div class="calendar-wrapper" id="calendarWrapper">
                            <?php foreach($weekDays as $dayNum => $dayName): ?>
                                <div class="day-column">
                                    <div class="day-header"><?php echo $dayName; ?></div>
                                    
                                    <?php if(isset($scheduleData[$dayNum])): ?>
                                        <?php foreach($scheduleData[$dayNum] as $lesson): 
                                            $subName = $subMap[$lesson['subject_id']] ?? 'Matéria removida';
                                            $profName = $profMap[$lesson['professor_id']] ?? 'Professor(a)';
                                        ?>
                                            <div class="lesson-card">
                                                <div class="lesson-time">
                                                    <i class='bx bx-time-five'></i> 
                                                    <?php echo substr($lesson['start_time'], 0, 5) . ' - ' . substr($lesson['end_time'], 0, 5); ?>
                                                </div>
                                                <div class="lesson-subject"><?php echo htmlspecialchars($subName); ?></div>
                                                
                                                <div class="lesson-prof-name">
                                                    <i class='bx bx-user'></i> <?php echo htmlspecialchars($profName); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div style="text-align: center; color: #ccc; padding: 20px 0; font-size: 0.9rem;">
                                            Sem aula
                                        </div>
                                    <?php endif; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button class="nav-arrow nav-next" id="nextBtn"><i class='bx bx-chevron-right'></i></button>
                    </div>
                <?php endif; ?>

            </div>
        </section>

        <script src="../../../js/sidebar-animation.js"></script>
        <script src="students-calendary.page.js"></script>
    </body>
</html>