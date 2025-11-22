<?php
    require_once '../professorSection.php';

    $profId = $_SESSION['user_id'];
    
    date_default_timezone_set('America/Sao_Paulo');
    $now = date('H:i:00');
    $dayOfWeek = date('w'); // 0 (dom) a 6 (sab)

    // ------------------------------------------------------------
    // 1. Lógica: Próxima Aula
    // ------------------------------------------------------------
    $queryNext = "professors_schedule?professor_id=eq.$profId&day_of_week=eq.$dayOfWeek&start_time=gt.$now&order=start_time.asc&limit=1&select=*,classes(class_name),subjects(name)";
    $nextClassData = \supabaseRestRequest($supabaseUrl, $supabaseKey, $queryNext, "GET", null, $_SESSION['access_token']);
    
    $hasNext = (!empty($nextClassData) && !isset($nextClassData['error']) && count($nextClassData) > 0);
    $nextClass = $hasNext ? $nextClassData[0] : null;

    // ------------------------------------------------------------
    // 2. Lógica: Total de Aulas Hoje
    // ------------------------------------------------------------
    $queryToday = "professors_schedule?professor_id=eq.$profId&day_of_week=eq.$dayOfWeek&select=id";
    $todayClasses = \supabaseRestRequest($supabaseUrl, $supabaseKey, $queryToday, "GET", null, $_SESSION['access_token']);
    $countToday = (!empty($todayClasses) && !isset($todayClasses['error'])) ? count($todayClasses) : 0;

    // ------------------------------------------------------------
    // 3. Lógica: Total de Turmas (Distintas) na Semana
    // ------------------------------------------------------------
    // É uma métrica interessante para mostrar o alcance do professor
    // Como o Supabase REST puro não tem "DISTINCT" simples no count sem RPC, vamos pegar todas e contar no PHP (se não forem muitas)
    $queryWeek = "professors_schedule?professor_id=eq.$profId&select=class_id";
    $weekClasses = \supabaseRestRequest($supabaseUrl, $supabaseKey, $queryWeek, "GET", null, $_SESSION['access_token']);
    
    $uniqueClasses = [];
    if (!empty($weekClasses) && !isset($weekClasses['error'])) {
        foreach($weekClasses as $wc) {
            $uniqueClasses[$wc['class_id']] = true;
        }
    }
    $countUniqueClasses = count($uniqueClasses);

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Painel do Professor</title>
        
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="professors-home.page.css">
        
        <link rel="stylesheet" href="../../admin/admins-professors/admins-professors.page.css"> 
    </head>
    <body>
    
        <?php include '../../../components/sidebars/professor-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-dashboard">

                <div class="welcome-banner">
                    <h1>Olá, <?php echo htmlspecialchars($userName); ?>!</h1>
                    <p>Este é o seu painel de controle pedagógico. Acompanhe sua agenda e gerencie suas turmas com facilidade.</p>
                </div>

                <div class="section-title">Sua Atividade Hoje</div>
                <div class="kpi-grid">
                    
                    <div class="kpi-card next-class <?php echo !$hasNext ? 'no-class-state' : ''; ?>">
                        <div class="kpi-icon"><i class='bx bx-time-five'></i></div>
                        <div class="kpi-info">
                            <?php if ($hasNext): ?>
                                <p>Próxima Aula</p>
                                <h3><?php echo substr($nextClass['start_time'], 0, 5); ?> - <?php echo htmlspecialchars($nextClass['classes']['class_name']); ?></h3>
                                <span class="kpi-sub"><?php echo htmlspecialchars($nextClass['subjects']['name']); ?></span>
                            <?php else: ?>
                                <p>Status</p>
                                <h3>Livre</h3>
                                <span class="kpi-sub">Nenhuma aula pendente hoje</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon" style="background: #f6ffed; color: #52c41a;"><i class='bx bx-calendar-check'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $countToday; ?></h3>
                            <p>Aulas Totais Hoje</p>
                        </div>
                    </div>

                    <div class="kpi-card">
                        <div class="kpi-icon" style="background: #fff7e6; color: #fa8c16;"><i class='bx bx-group'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $countUniqueClasses; ?></h3>
                            <p>Turmas Atendidas</p>
                        </div>
                    </div>

                </div>

                <div class="section-title">Acesso Rápido</div>
                <div class="shortcuts-grid">
                    
                    <a href="../professor-calendary/professor-calendary.page.php" class="shortcut-card">
                        <i class='bx bx-calendar'></i>
                        <span>Ver Grade Completa</span>
                    </a>

                    <a href="../professor-perfil/professor-perfil.page.php" class="shortcut-card">
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

            // Fecha ao clicar no fundo escuro
            policyModal.addEventListener('click', (e) => {
                if (e.target === policyModal) closePolicyModal();
            });
        </script>
    </body>
</html>