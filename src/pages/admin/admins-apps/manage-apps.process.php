<?php
session_start();
require_once(__DIR__ . '/../../../connect/connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

$action = $_POST['action'] ?? '';
$adminId = $_SESSION['user_id'];

// --- AÇÃO: DESBLOQUEAR APP ---
if ($action === 'unlock_app') {
    $appId = $_POST['app_id'];

    $data = [
        'p_admin_id' => $adminId,
        'p_app_id'   => $appId
    ];

    $response = supabaseRestRequest($supabaseUrl, $supabaseKey, "rpc/unlock_app_global_and_clean", "POST", $data, $_SESSION['access_token']);

    if (isset($response['error'])) {
        // Se quiser, pode passar o erro na URL para debugar: error=unlock_failed&msg=...
        header("Location: admins-apps.page.php?error=unlock_failed");
    } else {
        // O response aqui é o texto de sucesso, mas para o usuário basta redirecionar
        header("Location: admins-apps.page.php?success=unlocked");
    }
    exit;

// --- AÇÃO: BLOQUEAR APP ---
} elseif ($action === 'lock_app') {
    $appId = $_POST['app_id'];

    $query = "admins_apps?admin_id=eq.$adminId&app_id=eq.$appId";
    
    $response = supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "DELETE", null, $_SESSION['access_token']);

    if (isset($response['error'])) {
        header("Location: admins-apps.page.php?error=lock_failed");
    } else {
        header("Location: admins-apps.page.php?success=locked");
    }
    exit;
}

header("Location: admins-apps.page.php");
exit;
?>