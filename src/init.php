<?php
// Inicia a sessão globalmente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Carrega o Autoload e a Conexão (ajuste os caminhos se necessário)
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/connect/connect.php';

use Blome\Services\SupabaseClient;
use Blome\Services\UserProvider;
use Blome\Services\UserProfileLoader;

// Inicializa os serviços principais uma única vez
$client = new SupabaseClient($supabaseUrl, $supabaseKey);
$userProvider = new UserProvider($client);
$profileLoader = new UserProfileLoader($userProvider, $_SESSION);

/**
 * Função Helper para verificar permissão e retornar dados do usuário.
 * Se o usuário não tiver a role correta, redireciona para o login.
 */
function requireRole($requiredRole) {
    global $profileLoader;

    // Verifica se a role da sessão bate com a exigida (admin, professor, student)
    if ($profileLoader->getSessionRole() !== $requiredRole) {
        // Redireciona para o login (caminho absoluto a partir da raiz do site costuma ser mais seguro)
        // Se o seu projeto não estiver na raiz, ajuste para: /nome_da_pasta/src/pages/...
        header("Location: /src/pages/login/login.page.php?error=unauthorized");
        exit;
    }

    return $profileLoader->getProfileData();
}
?>