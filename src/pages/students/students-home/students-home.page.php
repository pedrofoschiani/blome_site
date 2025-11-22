<?php
    require_once '../studentSection.php';

    $studentId = $_SESSION['user_id'];
    date_default_timezone_set('America/Sao_Paulo');
    $now = date('H:i:00');
    $day = date('w');

    $studentData = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=class_id&id=eq.$studentId", "GET", null, $_SESSION['access_token']);

    $classId = null;
    $className = "Sua Turma";

    if(!empty($studentData) && isset($studentData[0]['class_id'])) {
        $classId = $studentData[0]['class_id'];
        
        $classInfo = \supabaseRestRequest($supabaseUrl, $supabaseKey, "classes?select=class_name&id=eq.$classId", "GET", null, $_SESSION['access_token']);
        if(!empty($classInfo) && isset($classInfo[0]['class_name'])) {
            $className = $classInfo[0]['class_name'];
        }
    }

    $status = "LIVRE";
    $statusClass = "status-free";
    $statusIcon = "bx-check-circle";
    $currentLessonEnd = null;
    
    $nextClassSubject = "Nenhuma";
    $nextClassTime = "--:--";
    
    $totalClassesToday = 0;

    if ($classId) {
        $queryNow = "professors_schedule?class_id=eq.$classId&day_of_week=eq.$day&start_time=lte.$now&end_time=gte.$now";
        $currentClass = \supabaseRestRequest($supabaseUrl, $supabaseKey, $queryNow, "GET", null, $_SESSION['access_token']);

        if(!empty($currentClass) && !isset($currentClass['error']) && count($currentClass) > 0) {
            $status = "BLOQUEADO";
            $statusClass = "status-blocked";
            $statusIcon = "bx-lock-alt";
            $currentLessonEnd = substr($currentClass[0]['end_time'], 0, 5);
        }

        $queryNext = "professors_schedule?class_id=eq.$classId&day_of_week=eq.$day&start_time=gt.$now&order=start_time.asc&limit=1&select=*,subjects(name)";
        $nextClass = \supabaseRestRequest($supabaseUrl, $supabaseKey, $queryNext, "GET", null, $_SESSION['access_token']);

        if(!empty($nextClass) && !isset($nextClass['error']) && count($nextClass) > 0) {
            $nextClassSubject = $nextClass[0]['subjects']['name'];
            $nextClassTime = substr($nextClass[0]['start_time'], 0, 5);
        }

        $queryCount = "professors_schedule?class_id=eq.$classId&day_of_week=eq.$day&select=id";
        $countData = \supabaseRestRequest($supabaseUrl, $supabaseKey, $queryCount, "GET", null, $_SESSION['access_token']);
        $totalClassesToday = (!empty($countData) && !isset($countData['error'])) ? count($countData) : 0;
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Estudantes</title>
        
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="students-home.page.css">
        <link rel="stylesheet" href="../../admin/admins-professors/admins-professors.page.css">
    </head>
    <body>
        <?php include '../../../components/sidebars/student-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-dashboard">

                <div class="welcome-banner">
                    <h1>Olá, <?php echo htmlspecialchars($userName); ?>!</h1>
                    <p>Bem-vindo ao seu espaço escolar. Mantenha o foco e bons estudos.</p>
                </div>

                <div class="section-title">Status Atual</div>
                <div class="kpi-grid">
                    
                    <div class="kpi-card <?php echo $statusClass; ?>">
                        <div class="kpi-icon"><i class='bx <?php echo $statusIcon; ?>'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $status; ?></h3>
                            <p>Dispositivo Móvel</p>
                            <?php if($status == "BLOQUEADO"): ?>
                                <span class="kpi-sub">Libera às <?php echo $currentLessonEnd; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon" style="background: #e6f7ff; color: #1890ff;"><i class='bx bx-time-five'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $nextClassTime; ?></h3>
                            <p>Próxima Aula</p>
                            <span class="kpi-sub"><?php echo $nextClassSubject; ?></span>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon" style="background: #fff7e6; color: #fa8c16;"><i class='bx bx-group'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo htmlspecialchars($className); ?></h3>
                            <p>Sua Turma</p>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon" style="background: #fff0f6; color: #eb2f96;"><i class='bx bx-calendar-check'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $totalClassesToday; ?></h3>
                            <p>Aulas Hoje</p>
                        </div>
                    </div>

                </div>

                <div class="section-title">Acesso Rápido</div>
                <div class="shortcuts-grid">
                    
                    <a href="../students-calendary/students-calendary.page.php" class="shortcut-card">
                        <i class='bx bx-calendar'></i>
                        <span>Minha Grade</span>
                    </a>

                    <a href="../students-perfil/students-perfil.page.php" class="shortcut-card">
                        <i class='bx bx-user-circle'></i>
                        <span>Meu Perfil</span>
                    </a>
                    
                </div>

            </div>

            <div class="floating-policy-btn" onclick="openPolicyModal()">
                <i class='bx bx-shield-quarter'></i>
            </div>

        </section>

         <?php include '../../../components/privacidade/privacidade.component.php' ?>

        <script src="../../../js/sidebar-animation.js"></script>
        
        <script>
            const policyModal = document.getElementById('policyModal');

            function openPolicyModal() {
                policyModal.classList.add('open');
                policyModal.style.display = 'flex';
            }

            function closePolicyModal() {
                policyModal.classList.remove('open');
                setTimeout(() => { policyModal.style.display = 'none'; }, 300);
            }

            policyModal.addEventListener('click', (e) => {
                if (e.target === policyModal) closePolicyModal();
            });
        </script>
    </body>
</html>