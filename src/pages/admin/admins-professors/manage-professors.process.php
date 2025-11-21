<?php
session_start();
require_once(__DIR__ . '/../../../connect/connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

$action = $_POST['action'] ?? '';

// --- PROFESSOR: CRIAR (Mantido) ---
if ($action === 'create') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $adminId = $_SESSION['user_id'];
    $adminData = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "admins_info?select=institution_id&id=eq.$adminId");
    $institutionId = $adminData[0]['institution_id'] ?? null;
    
    if (!$institutionId) die("Erro: Admin sem instituição.");

    $authResponse = supabaseAdminAuthRequest("users", "POST", ["email" => $email, "password" => $password, "email_confirm" => true]);
    
    if (isset($authResponse['id'])) {
        $newUserId = $authResponse['id'];
        $insertData = ['id' => $newUserId, 'full_name' => $name, 'institution_id' => $institutionId, 'avatar_url' => 'https://placehold.co/150'];
        supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_info", "POST", $insertData);
        header("Location: admins-professors.page.php?success=created");
    } else {
        header("Location: admins-professors.page.php?error=create_failed");
    }
    exit;

// --- PROFESSOR: DELETAR (Mantido) ---
} elseif ($action === 'delete') {
    $userId = $_POST['user_id'];
    supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects?professor_id=eq.$userId", "DELETE");
    supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_info?id=eq.$userId", "DELETE");
    supabaseAdminAuthRequest("users/$userId", "DELETE");
    header("Location: admins-professors.page.php?success=deleted");
    exit;

// --- PROFESSOR: ATUALIZAR MATÉRIAS (Mantido) ---
} elseif ($action === 'update_subjects') {
    $professorId = $_POST['professor_id'];
    $selectedSubjects = $_POST['subjects'] ?? [];

    supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects?professor_id=eq.$professorId", "DELETE");

    if (!empty($selectedSubjects)) {
        $insertBatch = [];
        foreach ($selectedSubjects as $subId) {
            $insertBatch[] = ['professor_id' => $professorId, 'subject_id' => (int)$subId];
        }
        supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects", "POST", $insertBatch);
    }
    header("Location: admins-professors.page.php?success=subjects_updated");
    exit;

// --- NOVO: CRIAR MATÉRIA ---
} elseif ($action === 'create_subject') {
    $subjectName = $_POST['subject_name'];

    if (!empty($subjectName)) {
        $insertData = ['name' => $subjectName];
        
        $response = supabaseRestRequest(
            $supabaseUrl, 
            $supabaseServiceKey, 
            "subjects", 
            "POST", 
            $insertData
        );

        if (isset($response['error'])) {
            header("Location: admins-professors.page.php?error=subject_create_failed");
        } else {
            header("Location: admins-professors.page.php?success=subject_created");
        }
    } else {
        header("Location: admins-professors.page.php?error=empty_name");
    }
    exit;

// --- AÇÃO: DELETAR MATÉRIA ---
} elseif ($action === 'delete_subject') {
    $subjectId = $_POST['subject_id'];

    // 1. Primeiro deleta as relações na tabela pivot (professors_subjects)
    // Se não fizer isso, o banco pode bloquear a exclusão se houver FK
    supabaseRestRequest(
        $supabaseUrl,
        $supabaseServiceKey,
        "professors_subjects?subject_id=eq.$subjectId",
        "DELETE"
    );

    // 2. Deleta a matéria
    $response = supabaseRestRequest(
        $supabaseUrl,
        $supabaseServiceKey,
        "subjects?id=eq.$subjectId",
        "DELETE"
    );

    if (isset($response['error'])) {
        header("Location: admins-professors.page.php?error=subject_delete_failed");
    } else {
        header("Location: admins-professors.page.php?success=subject_deleted");
    }
    exit;
}
?>