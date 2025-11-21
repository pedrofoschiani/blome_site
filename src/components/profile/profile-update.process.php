<?php
// 1. Inicia a sessão (essencial)
session_start();

// 2. Includes
require_once(__DIR__ . '/../../../vendor/autoload.php');
require_once(__DIR__ . '/../../connect/connect.php');

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido ou expirado. Atualize a página e tente novamente.");
}

// 3. Define quais classes vamos usar
use Blome\Services\SupabaseClient;
use Blome\Services\ProfileUpdater;
use Blome\Services\ProfileUpdateHandler; // <-- A nova classe

// 4. Instancia as dependências REAIS
$client = new SupabaseClient($supabaseUrl, $supabaseKey);
$updater = new ProfileUpdater($client);

// 5. Instancia o Handler com os dados REAIS
$handler = new ProfileUpdateHandler(
    $updater, 
    $_SESSION, 
    $_POST, 
    $_FILES,
    $_SERVER
);

// 6. Processa o pedido e obtém a URL de redirecionamento
$redirectUrl = $handler->handleRequest();

// 7. Se a autenticação mudou, destrói a sessão
if (strpos($redirectUrl, 'success=auth_updated') !== false) {
    session_unset();
    session_destroy();
}

// 8. Redireciona
header("Location: " . $redirectUrl);
exit;
?>