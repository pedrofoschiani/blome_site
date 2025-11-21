<?php
session_start();
require_once __DIR__ . '/../../../init.php';

// Carrega o novo Service
require_once __DIR__ . '/../../../Services/ProfessorService.php';

// --- SEGURANÇA ---
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido.");
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

use Blome\Services\ProfessorService;

$profService = new ProfessorService($client);
$action = $_POST['action'] ?? '';
$token = $_SESSION['access_token'];
$adminId = $_SESSION['user_id'];

// Função local para tratar criação/deleção de MATÉRIAS (que são simples demais para o Service ainda)
// Se quiser, pode mover isso para um SubjectService no futuro.
function handleSubjectAction($action, $supabaseUrl, $supabaseServiceKey) {
    if ($action === 'create_subject') {
        $name = $_POST['subject_name'] ?? '';
        if (empty($name)) header("Location: admins-professors.page.php?error=empty_name");
        
        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "subjects", "POST", ['name' => $name], null);
        if (isset($res['error'])) header("Location: admins-professors.page.php?error=subject_create_failed");
        else header("Location: admins-professors.page.php?success=subject_created");
        exit;

    } elseif ($action === 'delete_subject') {
        $id = $_POST['subject_id'];
        // Deleta vínculos primeiro
        \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects?subject_id=eq.$id", "DELETE", null, null);
        // Deleta a matéria
        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "subjects?id=eq.$id", "DELETE", null, null);
        
        if (isset($res['error'])) header("Location: admins-professors.page.php?error=subject_delete_failed");
        else header("Location: admins-professors.page.php?success=subject_deleted");
        exit;
    }
}

// Verifica se é ação de Matéria primeiro
if ($action === 'create_subject' || $action === 'delete_subject') {
    handleSubjectAction($action, $supabaseUrl, $supabaseServiceKey);
}

try {
    // --- AÇÕES DE PROFESSOR (Usando o Service) ---

    if ($action === 'create') {
        $result = $profService->createProfessor(
            $_POST['name'], 
            $_POST['email'], 
            $_POST['password'], 
            $adminId, 
            $token
        );

        if (isset($result['success'])) {
            header("Location: admins-professors.page.php?success=created");
        } else {
            header("Location: admins-professors.page.php?error=" . ($result['error'] ?? 'create_failed'));
        }

    } elseif ($action === 'delete') {
        $success = $profService->deleteProfessor($_POST['user_id']);
        
        if ($success) header("Location: admins-professors.page.php?success=deleted");
        else header("Location: admins-professors.page.php?error=delete_failed");

    } elseif ($action === 'update_subjects') {
        $profId = $_POST['professor_id'];
        $subjects = $_POST['subjects'] ?? [];

        $success = $profService->updateSubjects($profId, $subjects);

        if ($success) header("Location: admins-professors.page.php?success=subjects_updated");
        else header("Location: admins-professors.page.php?error=update_failed");

    } else {
        header("Location: admins-professors.page.php");
    }

} catch (Exception $e) {
    header("Location: admins-professors.page.php?error=exception");
}
exit;
?>