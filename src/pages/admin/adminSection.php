<?php
// 1. Inicia a sessão
session_start();

// 2. Includes
require_once(__DIR__ . '/../../../vendor/autoload.php');
require_once(__DIR__ . '/../../connect/connect.php');

// 3. Define quais classes vamos usar
use Blome\Services\SupabaseClient;
use Blome\Services\UserProvider;
use Blome\Services\UserProfileLoader;

// 4. Injeta as dependências
$client = new SupabaseClient($supabaseUrl, $supabaseKey);
$userProvider = new UserProvider($client);

// 5. Injeta a SESSÃO REAL na nossa classe testada
$profileLoader = new UserProfileLoader($userProvider, $_SESSION);

// 6. Verificação de Segurança
if ($profileLoader->getSessionRole() !== 'admin') {
    header("Location: ../login/login.page.php?error=unauthorized");
    exit;
}

// 7. Busca os dados do perfil
$profileData = $profileLoader->getProfileData();

// 8. Define as variáveis para a View (HTML)
$userName = $profileData['full_name'];
$userAvatar = $profileData['avatar_url'];

?>