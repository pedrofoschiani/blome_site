<?php
    require_once '../professorSection.php';

    $profId = $_SESSION['user_id'];
    
    // Busca a PRÓXIMA aula de hoje
    date_default_timezone_set('America/Sao_Paulo');
    $now = date('H:i:00');
    $day = date('w');

    // Query: Aula do professor, hoje, que começa depois de agora, ordena pela mais cedo, pega 1.
    $query = "professors_schedule?professor_id=eq.$profId&day_of_week=eq.$day&start_time=gt.$now&order=start_time.asc&limit=1&select=*,classes(class_name),subjects(name)";
    
    $nextClass = \supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "GET", null, $_SESSION['access_token']);
    
    $hasClass = (!empty($nextClass) && !isset($nextClass['error']) && count($nextClass) > 0);
    $aula = $hasClass ? $nextClass[0] : null;
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Professores</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <style>
            .welcome-header { margin: 100px auto 30px; max-width: 800px; padding: 0 20px; }
            .welcome-header h1 { color: var(--text-color-dark); font-size: 2rem; font-weight: 800; }
            .welcome-header p { color: #666; font-size: 1.1rem; margin-top: 5px; }

            .next-class-card {
                background: #fff;
                border-radius: 35px;
                padding: 40px;
                max-width: 800px;
                margin: 0 auto;
                box-shadow: 0 15px 40px rgba(0,0,0,0.05);
                border-left: 10px solid var(--primary-color);
                display: flex; flex-direction: column; gap: 15px;
            }
            .nc-label { text-transform: uppercase; letter-spacing: 1px; font-size: 0.9rem; color: #999; font-weight: 700; }
            .nc-title { font-size: 2.5rem; font-weight: 800; color: var(--primary-color); }
            .nc-info { display: flex; gap: 30px; margin-top: 10px; }
            .nc-item { display: flex; align-items: center; gap: 10px; font-size: 1.2rem; color: #555; font-weight: 600; }
            .nc-item i { color: var(--accent-color); font-size: 1.4rem; }
            
            .no-class { text-align: center; color: #999; padding: 40px; background: #fff; border-radius: 35px; max-width: 800px; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.05);}
        </style>
    </head>
    <body>
        <?php include '../../../components/sidebars/professor-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="welcome-header">
                <h1>Bem-vindo, Professor(a)!</h1>
                <p>Confira sua próxima atividade programada.</p>
            </div>

            <?php if($hasClass): ?>
                <div class="next-class-card">
                    <span class="nc-label">Próxima Aula Hoje</span>
                    <h2 class="nc-title"><?php echo htmlspecialchars($aula['subjects']['name']); ?></h2>
                    <div class="nc-info">
                        <div class="nc-item">
                            <i class='bx bx-time-five'></i> 
                            <?php echo substr($aula['start_time'], 0, 5) . ' - ' . substr($aula['end_time'], 0, 5); ?>
                        </div>
                        <div class="nc-item">
                            <i class='bx bx-building'></i> 
                            <?php echo htmlspecialchars($aula['classes']['class_name']); ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-class">
                    <i class='bx bx-coffee' style="font-size: 4rem; margin-bottom: 15px; display: block; color: #ddd;"></i>
                    <h3>Tudo livre por hoje!</h3>
                    <p>Você não tem mais aulas agendadas para o dia de hoje.</p>
                </div>
            <?php endif; ?>

        </section>

        <script src="../../../js/sidebar-animation.js"></script>
    </body>
</html>