<?php
    require_once '../adminSection.php';

    // 1. Busca Apps Desbloqueados (admins_apps)
    $myAppsRaw = \supabaseRestRequest($supabaseUrl, $supabaseKey, "admins_apps?select=*,apps(*)&admin_id=eq." . $_SESSION['user_id'], "GET", null, $_SESSION['access_token']);

    // 2. Busca TODOS os Apps (apps)
    $allApps = \supabaseRestRequest($supabaseUrl, $supabaseKey, "apps?select=*", "GET", null, $_SESSION['access_token']);

    $myUnlockedAppIds = [];
    $myAppsList = [];

    if (!empty($myAppsRaw) && !isset($myAppsRaw['error'])) {
        foreach ($myAppsRaw as $row) {
            if (isset($row['apps'])) {
                $myAppsList[] = $row['apps'];
                $myUnlockedAppIds[] = $row['apps']['id'];
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BLOME | Gerenciar Apps</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        
        <link rel="stylesheet" href="admins-apps.page.css">
    </head>
    <body>
    
        <?php include '../../../components/sidebars/admin-sidebar.component.php'; ?>
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">
            <?php include '../../../components/header/header.component.php'; ?>

            <div class="container-prof">
            
                <?php if(isset($_GET['success'])): ?>
                    <div class="msg-box msg-success">
                        <?php 
                            if($_GET['success'] == 'unlocked') echo "App desbloqueado! Ele foi removido das listas individuais das turmas.";
                            else if($_GET['success'] == 'locked') echo "App bloqueado novamente.";
                        ?>
                    </div>
                <?php elseif(isset($_GET['error'])): ?>
                    <div class="msg-box msg-error">Erro ao processar solicitação. Tente novamente.</div>
                <?php endif; ?>
                
                <div class="card-form">
                    <div class="card-header-wrapper">
                        <div class="section-header">
                            <i class='bx bx-lock-open-alt'></i>
                            <h3>Apps Desbloqueados para a Escola</h3>
                        </div>
                        <p class="section-desc">Estes aplicativos estão disponíveis para todas as turmas da instituição.</p>
                    </div>

                    <div class="table-responsive-wrapper">
                        <table class="table-list">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Ícone</th>
                                    <th>Nome do App</th>
                                    <th>Pacote</th>
                                    <th style="text-align: center;">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($myAppsList)): ?>
                                    <?php foreach($myAppsList as $app): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($app['icon_url'] ?? 'https://placehold.co/50'); ?>" class="app-icon">
                                        </td>
                                        <td><span class="app-name"><?php echo htmlspecialchars($app['app_name']); ?></span></td>
                                        <td style="font-size: 0.85rem; color: #888;"><?php echo htmlspecialchars($app['package_name']); ?></td>
                                        <td style="display: flex; justify-content: center;">
                                            <form action="manage-apps.process.php" method="POST">
                                                <input type="hidden" name="action" value="lock_app">
                                                <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                                <button type="submit" class="btn-action btn-delete" title="Bloquear App">
                                                    <i class='bx bx-lock-alt'></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" style="text-align:center; padding: 30px; color: #888;">Nenhum aplicativo desbloqueado ainda.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-form">
                    
                    <div class="header-with-search">
                        <div class="title-part">
                            <div class="section-header">
                                <i class='bx bx-grid-alt'></i>
                                <h3>Biblioteca de Apps Disponíveis</h3>
                            </div>
                            <p class="section-desc">Adicione à lista da escola para permitir o uso.</p>
                        </div>

                        <div class="search-box">
                            <i class='bx bx-search'></i>
                            <input type="text" id="searchAppInput" placeholder="Pesquisar aplicativo...">
                        </div>
                    </div>

                    <div class="table-responsive-wrapper">
                        <table class="table-list" id="libraryTable">
                            <thead>
                                <tr>
                                    <th style="width: 70px;">Ícone</th>
                                    <th>Nome do App</th>
                                    <th>Pacote</th>
                                    <th style="text-align: center;">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($allApps) && !isset($allApps['error'])): ?>
                                    <?php foreach($allApps as $app): ?>
                                        <?php if(in_array($app['id'], $myUnlockedAppIds)) continue; ?>
                                    <tr class="app-row">
                                        <td>
                                            <img src="<?php echo htmlspecialchars($app['icon_url'] ?? 'https://placehold.co/50'); ?>" class="app-icon">
                                        </td>
                                        <td><span class="app-name search-target"><?php echo htmlspecialchars($app['app_name']); ?></span></td>
                                        <td style="font-size: 0.85rem; color: #888;"><?php echo htmlspecialchars($app['package_name']); ?></td>
                                        <td style="display: flex; justify-content: center;">
                                            <form action="manage-apps.process.php" method="POST">
                                                <input type="hidden" name="action" value="unlock_app">
                                                <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                                                <button type="submit" class="btn-action btn-subjects" title="Desbloquear para Escola">
                                                    <i class='bx bx-lock-open-alt'></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" style="text-align:center; padding: 30px;">Nenhum aplicativo encontrado no banco.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                        <div id="noResultsMsg" style="display:none; text-align:center; padding: 20px; color: #666;">Nenhum app encontrado com este nome.</div>
                    </div>
                </div>

            </div>
        </section>

        <script src="../../../js/sidebar-animation.js"></script>
        <script src="../../../js/message-handler.js"></script>
        
        <script>
            // Script de Pesquisa
            document.getElementById('searchAppInput').addEventListener('keyup', function() {
                const query = this.value.toLowerCase();
                const rows = document.querySelectorAll('.app-row');
                let hasVisible = false;

                rows.forEach(row => {
                    const appName = row.querySelector('.search-target').innerText.toLowerCase();
                    // Pesquisa por nome ou pacote
                    if (appName.includes(query)) {
                        row.style.display = ''; // Mostra
                        hasVisible = true;
                    } else {
                        row.style.display = 'none'; // Esconde
                    }
                });

                const noMsg = document.getElementById('noResultsMsg');
                if(noMsg) noMsg.style.display = hasVisible ? 'none' : 'block';
            });
        </script>
    </body>
</html>