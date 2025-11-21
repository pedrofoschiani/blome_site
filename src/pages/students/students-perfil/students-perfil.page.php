<?php
    require_once '../studentSection.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE-edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <title>BLOME | Estudantes</title>

        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        
        <link rel="stylesheet" href="../../../style/global.css">
        <link rel="stylesheet" href="../../../style/variable.css">
        <link rel="stylesheet" href="../../../style/dashboard-layout.css">
        <link rel="stylesheet" href="../../../components/header/header.component.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        
        <link rel="stylesheet" href="students-perfil.page.css">
    </head>
    <body>

    
        <?php include '../../../components/sidebars/student-sidebar.component.php'; ?>
        
        <i class='bx bx-menu toggle-open'></i>

        <section class="home">

            <?php include '../../../components/header/header.component.php'; ?>

            <div class="main-content"> <?php include '../../../components/profile/profile-content.component.php'; ?>
            </div>

        </section>

        <script src="../../../js/sidebar-animation.js"></script>
    </body>
</html>