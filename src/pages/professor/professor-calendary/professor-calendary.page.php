<?php
    require_once '../professorSection.php';

    // O ID do professor logado vem da sessão (via professorSection.php -> UserProfileLoader)
    $myId = $_SESSION['user_id'];

    // 1. Buscas no Banco de Dados
    
    // Buscar Matérias para mapear ID -> Nome
    $subjects = \supabaseRestRequest($supabaseUrl, $supabaseKey, "subjects?select=*", "GET", null, $_SESSION['access_token']);
    
    // Buscar Salas para mapear ID -> Nome
    $classes = \supabaseRestRequest($supabaseUrl, $supabaseKey, "classes?select=*", "GET", null, $_SESSION['access_token']);

    // Buscar APENAS o horário DESTE professor
    // Ordenado por dia da semana e horário de início
    $query = "professors_schedule?professor_id=eq.$myId&order=day_of_week.asc,start_time.asc";
    $rawSchedule = \supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "GET", null, $_SESSION['access_token']);

    // 2. Criação dos Mapas (Para facilitar a exibição)
    $subMap = [];
    if (!empty($subjects) && !isset($subjects['error'])) {
        foreach($subjects as $s) $subMap[$s['id']] = $s['name'];
    }

    $classMap = [];
    if (!empty($classes) && !isset($classes['error'])) {
        foreach($classes as $c) $classMap[$c['id']] = $c['class_name'];
    }

    // 3. Organizar dados por Dia da Semana
    $scheduleData = [];
    if (!empty($rawSchedule) && !isset($rawSchedule['error'])) {
        foreach($rawSchedule as $item) {
            $scheduleData[$item['day_of_week']][] = $item;
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
        <title>BLOME | Minha Agenda</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="professor-calendary.page.css">
    </head>
    <body>
        <?php include '../../../components/sidebars/professor-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-prof">

                <div class="calendar-container">
                    <button class="nav-arrow nav-prev" id="prevBtn"><i class='bx bx-chevron-left'></i></button>
                    
                    <div class="calendar-wrapper" id="calendarWrapper">
                        <?php foreach($weekDays as $dayNum => $dayName): ?>
                            <div class="day-column">
                                <div class="day-header"><?php echo $dayName; ?></div>
                                
                                <?php if(isset($scheduleData[$dayNum])): ?>
                                    <?php foreach($scheduleData[$dayNum] as $lesson): 
                                        // Pegamos o nome da Matéria e da Sala pelos IDs
                                        $subName = $subMap[$lesson['subject_id']] ?? 'Matéria removida';
                                        $className = $classMap[$lesson['class_id']] ?? 'Sala removida';
                                    ?>
                                        <div class="lesson-card">
                                            <div class="lesson-time">
                                                <i class='bx bx-time-five'></i> 
                                                <?php echo substr($lesson['start_time'], 0, 5) . ' - ' . substr($lesson['end_time'], 0, 5); ?>
                                            </div>
                                            <div class="lesson-subject"><?php echo htmlspecialchars($subName); ?></div>
                                            
                                            <div class="lesson-class-name">
                                                <i class='bx bx-building'></i> <?php echo htmlspecialchars($className); ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div style="text-align: center; color: #ccc; padding: 20px 0; font-size: 0.9rem;">
                                        Livre
                                    </div>
                                <?php endif; ?>

                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="nav-arrow nav-next" id="nextBtn"><i class='bx bx-chevron-right'></i></button>
                </div>

            </div>
        </section>

        <script src="../../../js/sidebar-animation.js"></script>
        
        <script src="professor-calendary.page.js"></script>
    </body>
</html>