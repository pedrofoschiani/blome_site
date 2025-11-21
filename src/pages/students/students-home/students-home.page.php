<?php
    require_once '../studentSection.php';

    // 1. Descobrir se está em horário de aula (Lógica simplificada baseada no horário local)
    // Nota: Para produção ideal, usaríamos a query no banco igual fizemos no Ionic,
    // mas para o Dashboard Web, uma verificação visual basta.
    
    $status = "LIVRE";
    $statusColor = "#d1e7dd"; // Verde
    $statusText = "#0f5132";
    $statusIcon = "bx-check-circle";
    $message = "Você não tem aulas agora. Seu celular está desbloqueado.";

    // Pega turma do aluno
    $studentId = $_SESSION['user_id'];
    $studentData = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=class_id&id=eq.$studentId", "GET", null, $_SESSION['access_token']);
    
    if(!empty($studentData) && isset($studentData[0]['class_id'])) {
        $classId = $studentData[0]['class_id'];
        
        // Verifica no banco se tem aula AGORA
        date_default_timezone_set('America/Sao_Paulo');
        $now = date('H:i:00');
        $day = date('w'); // 0-6

        $query = "professors_schedule?class_id=eq.$classId&day_of_week=eq.$day&start_time=lte.$now&end_time=gte.$now";
        $currentClass = \supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "GET", null, $_SESSION['access_token']);

        if(!empty($currentClass) && !isset($currentClass['error']) && count($currentClass) > 0) {
            $status = "BLOQUEADO";
            $statusColor = "#f8d7da"; // Vermelho
            $statusText = "#842029";
            $statusIcon = "bx-lock-alt";
            $message = "Verifique no aplicativo BLOME os apps liberados. <p style='margin-top:15px;'> Sua próxima aula termina às " . $currentClass[0]['end_time'] . ". </p>";
        }
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
        
        <style>
            .status-card {
                background-color: <?php echo $statusColor; ?>;
                color: <?php echo $statusText; ?>;
                padding: 40px;
                border-radius: 35px;
                text-align: center;
                margin: 120px auto 0;
                max-width: 600px;
                box-shadow: 0 15px 40px rgba(0,0,0,0.08);
            }
            .status-icon { font-size: 4rem; margin-bottom: 15px; }
            .status-title { font-size: 2rem; font-weight: 800; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
            .status-msg { font-size: 1.1rem; opacity: 0.9; }
        </style>
    </head>
    <body>
        <?php include '../../../components/sidebars/student-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="status-card">
                <i class='bx <?php echo $statusIcon; ?> status-icon'></i>
                <h1 class="status-title"><?php echo $status; ?></h1>
                <p class="status-msg"><?php echo $message; ?></p>
            </div>
            
            <!-- AQUI ENTRARÃO OS CARDS DOS PROFESSORES (DO SEU COLEGA) -->
            
        </section>

        <script src="../../../js/sidebar-animation.js"></script>
    </body>
</html>