<!DOCTYPE html>
<html lang="pt-br">

<header class="top-bar">
    <div class="top-bar__content">
        
        <i class='bx bx-menu toggle-open-header'></i>

        <nav class="navbar">
            <ul class="nav-list">

                <li class="nav-item nav-item--account">
                    <div id="account" class="account-profile">
                        <img src="<?php echo htmlspecialchars($userAvatar); ?>" 
                             alt="Avatar de <?php echo htmlspecialchars($userName); ?>" 
                             class="profile-img">
                        <span class="profile-name"><?php echo htmlspecialchars($userName); ?></span>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="../../login/logout.php" id="exit-button" class="exit-button">
                        <i class='bx bx-log-out'></i>
                        <span class="exit-text">Sair</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>