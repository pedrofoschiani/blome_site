<?php
    require_once '../adminSection.php';

    $profs = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_info?select=id", "GET", null, $_SESSION['access_token']);
    $totalProfs = (!empty($profs) && !isset($profs['error'])) ? count($profs) : 0;

    $studs = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=id", "GET", null, $_SESSION['access_token']);
    $totalStuds = (!empty($studs) && !isset($studs['error'])) ? count($studs) : 0;

    $studsNoClass = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=id&class_id=is.null", "GET", null, $_SESSION['access_token']);
    $countNoClass = (!empty($studsNoClass) && !isset($studsNoClass['error'])) ? count($studsNoClass) : 0;

    $apps = \supabaseRestRequest($supabaseUrl, $supabaseKey, "admins_apps?select=id&admin_id=eq." . $_SESSION['user_id'], "GET", null, $_SESSION['access_token']);
    $totalApps = (!empty($apps) && !isset($apps['error'])) ? count($apps) : 0;

    date_default_timezone_set('America/Sao_Paulo');
    $dayOfWeek = date('w');
    
    $classesToday = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_schedule?select=id&day_of_week=eq.$dayOfWeek", "GET", null, $_SESSION['access_token']);
    $totalClassesToday = (!empty($classesToday) && !isset($classesToday['error'])) ? count($classesToday) : 0;

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Painel Administrativo</title>
        
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="admins-home.page.css">
        <link rel="stylesheet" href="../admins-professors/admins-professors.page.css"> </head>
    <body>
    
        <?php include '../../../components/sidebars/admin-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-dashboard">

                <div class="welcome-banner">
                    <h1>Olá, <?php echo htmlspecialchars($userName); ?>!</h1>
                    <p>Bem-vindo ao centro de controle da BLOME. Aqui está o resumo das atividades da sua instituição para hoje.</p>
                </div>

                <div class="section-title">Resumo da Instituição</div>
                <div class="kpi-grid">
                    
                    <div class="kpi-card" onclick="window.location.href='../admins-professors/admins-professors.page.php?filter=none'" style="cursor: pointer;">
                        <div class="kpi-icon icon-profs"><i class='bx bx-chalkboard'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $totalProfs; ?></h3>
                            <p>Professores</p>
                        </div>
                    </div>

                    <div class="kpi-card" onclick="window.location.href='../admins-students/admins-students.page.php?filter=none'" style="cursor: pointer;">
                        <div class="kpi-icon icon-studs"><i class='bx bx-user'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $totalStuds; ?></h3>
                            <p>Total Alunos</p>
                        </div>
                    </div>
                    
                    <div class="kpi-card" onclick="window.location.href='../admins-students/admins-students.page.php?filter=none'" style="cursor: pointer;">
                        <div class="kpi-icon icon-alert"><i class='bx bx-error-circle'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $countNoClass; ?></h3>
                            <p>Alunos sem Sala</p>
                        </div>
                    </div>

                    <div class="kpi-card" onclick="window.location.href='../admins-apps/admins-apps.page.php?filter=none'" style="cursor: pointer;">
                        <div class="kpi-icon icon-apps"><i class='bx bx-layer'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $totalApps; ?></h3>
                            <p>Apps Liberados</p>
                        </div>
                    </div>

                    <div class="kpi-card" onclick="window.location.href='../admins-calendary/admins-calendary.page.php?filter=none'" style="cursor: pointer;">
                        <div class="kpi-icon" style="background: #f6ffed; color: #52c41a;"><i class='bx bx-calendar-check'></i></div>
                        <div class="kpi-info">
                            <h3><?php echo $totalClassesToday; ?></h3>
                            <p>Aulas Hoje</p>
                        </div>
                    </div>

                </div>

                <div class="section-title">Acesso Rápido</div>
                <div class="shortcuts-grid">
                    
                    <a href="../admins-professors/admins-professors.page.php" class="shortcut-card">
                        <i class='bx bx-user-plus'></i>
                        <span>Cadastrar Professor</span>
                    </a>

                    <a href="../admins-students/admins-students.page.php" class="shortcut-card">
                        <i class='bx bx-group'></i>
                        <span>Gerenciar Alunos</span>
                    </a>

                    <a href="../admins-apps/admins-apps.page.php" class="shortcut-card">
                        <i class='bx bx-lock-open-alt'></i>
                        <span>Desbloquear App</span>
                    </a>

                    <a href="../admins-calendary/admins-calendary.page.php" class="shortcut-card">
                        <i class='bx bx-calendar-edit'></i>
                        <span>Editar Grade</span>
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
                policyModal.style.display = 'flex'; // Garante flex
            }

            function closePolicyModal() {
                policyModal.classList.remove('open');
                setTimeout(() => { policyModal.style.display = 'none'; }, 300);
            }

            // Fecha ao clicar fora
            policyModal.addEventListener('click', (e) => {
                if (e.target === policyModal) closePolicyModal();
            });
        </script>
    </body>
</html>