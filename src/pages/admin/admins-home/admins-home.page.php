<?php
    require_once '../adminSection.php';

    // 1. Buscar Contagens (Simples e Direto)
    // Trazemos apenas o ID para contar no PHP, economizando dados.
    
    // Total Professores
    $profs = \supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_info?select=id", "GET", null, $_SESSION['access_token']);
    $totalProfs = (!empty($profs) && !isset($profs['error'])) ? count($profs) : 0;

    // Total Alunos
    $studs = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=id", "GET", null, $_SESSION['access_token']);
    $totalStuds = (!empty($studs) && !isset($studs['error'])) ? count($studs) : 0;

    // Total Apps Liberados
    // Filtramos apenas os desse admin para mostrar o impacto dele
    $apps = \supabaseRestRequest($supabaseUrl, $supabaseKey, "admins_apps?select=id&admin_id=eq." . $_SESSION['user_id'], "GET", null, $_SESSION['access_token']);
    $totalApps = (!empty($apps) && !isset($apps['error'])) ? count($apps) : 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Administradores</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../admin-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <!-- Estilo Inline Específico para Dashboard Cards -->
        <style>
            .dashboard-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 25px;
                padding: 20px;
                margin-top: 20px;
                max-width: 1200px;
                margin-left: auto;
                margin-right: auto;
            }
            .dash-card {
                background: #fff;
                border-radius: 30px;
                padding: 30px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.05);
                display: flex;
                align-items: center;
                gap: 20px;
                transition: transform 0.3s ease;
            }
            .dash-card:hover { transform: translateY(-5px); }
            
            .dash-icon {
                width: 70px; height: 70px;
                border-radius: 20px;
                display: flex; align-items: center; justify-content: center;
                font-size: 32px;
            }
            /* Cores dos Icones */
            .icon-profs { background: #eef2ff; color: var(--primary-color); }
            .icon-studs { background: #e6f7ff; color: var(--accent-color); }
            .icon-apps  { background: #fff0f6; color: #eb2f96; }

            .dash-info h3 { font-size: 2.5rem; font-weight: 800; color: var(--text-color-dark); margin: 0; line-height: 1; }
            .dash-info p { color: #888; font-size: 1rem; margin-top: 5px; font-weight: 600; }

            .welcome-banner {
                background: var(--gradient);
                color: #fff;
                padding: 40px;
                border-radius: 35px;
                margin: 100px 20px 20px 20px; /* Margem superior para header */
                max-width: 1200px;
                margin-left: auto; margin-right: auto;
                box-shadow: 0 15px 40px rgba(91, 52, 235, 0.3);
            }
            .welcome-banner h1 { font-size: 2rem; margin-bottom: 10px; }
            .welcome-banner p { font-size: 1.1rem; opacity: 0.9; }
        </style>
    </head>
    <body>
    
        <?php include '../../../components/sidebars/admin-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <!-- Banner de Boas Vindas -->
            <div class="welcome-banner">
                <h1>Olá, <?php echo htmlspecialchars($userName); ?>!</h1>
                <p>Bem-vindo ao painel administrativo. Aqui está o resumo da sua instituição hoje.</p>
            </div>

            <!-- Cards de Métricas -->
            <div class="dashboard-grid">
                
                <!-- Card Professores -->
                <div class="dash-card">
                    <div class="dash-icon icon-profs"><i class='bx bx-chalkboard'></i></div>
                    <div class="dash-info">
                        <h3><?php echo $totalProfs; ?></h3>
                        <p>Professores</p>
                    </div>
                </div>

                <!-- Card Alunos -->
                <div class="dash-card">
                    <div class="dash-icon icon-studs"><i class='bx bx-user'></i></div>
                    <div class="dash-info">
                        <h3><?php echo $totalStuds; ?></h3>
                        <p>Alunos Ativos</p>
                    </div>
                </div>

                <!-- Card Apps -->
                <div class="dash-card">
                    <div class="dash-icon icon-apps"><i class='bx bx-layer'></i></div>
                    <div class="dash-info">
                        <h3><?php echo $totalApps; ?></h3>
                        <p>Apps Liberados</p>
                    </div>
                </div>

            </div>

        </section>

        <script src="../../../js/sidebar-animation.js"></script>
    </body>
</html>