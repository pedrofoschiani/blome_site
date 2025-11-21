<?php
session_start();
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Services/AppService.php';

// --- SEGURANÇA ---
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido.");
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

use Blome\Services\AppService;

$appService = new AppService($client);
$action = $_POST['action'] ?? '';
$adminId = $_SESSION['user_id'];
$token = $_SESSION['access_token'];

try {
    // --- DESBLOQUEAR APP ---
    if ($action === 'unlock_app') {
        $appId = $_POST['app_id'];
        
        $response = $appService->unlockApp($adminId, $appId, $token);

        if (isset($response['error'])) {
            header("Location: admins-apps.page.php?error=unlock_failed");
        } else {
            header("Location: admins-apps.page.php?success=unlocked");
        }

    // --- BLOQUEAR APP ---
    } elseif ($action === 'lock_app') {
        $appId = $_POST['app_id'];

        $success = $appService->lockApp($adminId, $appId, $token);

        if ($success) {
            header("Location: admins-apps.page.php?success=locked");
        } else {
            header("Location: admins-apps.page.php?error=lock_failed");
        }

    } else {
        header("Location: admins-apps.page.php");
    }

} catch (Exception $e) {
    header("Location: admins-apps.page.php?error=exception");
}
exit;
?>