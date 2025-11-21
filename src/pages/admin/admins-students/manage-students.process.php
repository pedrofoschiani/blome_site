<?php
session_start();
require_once __DIR__ . '/../../../init.php'; 
require_once __DIR__ . '/../../../Services/StudentService.php'; 

// --- SEGURANÇA ---
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido.");
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

use Blome\Services\StudentService;

$studentService = new StudentService($client);

$action = $_POST['action'] ?? '';
$token = $_SESSION['access_token'];
$adminId = $_SESSION['user_id'];

// --- CORREÇÃO AQUI ---
function isSupabaseError($response) {
    // 1. Se a resposta for nula, significa HTTP 201/204 (Sucesso sem corpo)
    if ($response === null) return false; 
    
    // 2. Verifica erros padrões do Supabase
    if (isset($response['error'])) return true;
    
    // 3. Verifica mensagens de erro sem ID (ex: violação de constraint)
    if (isset($response['message']) && !isset($response['id']) && !isset($response[0])) return true;
    
    // 4. Verifica códigos SQL de erro (que não começam com 2)
    if (isset($response['code']) && is_string($response['code']) && strpos($response['code'], '2') !== 0) return true;
    
    return false;
}

try {
    // ============================================================
    // AÇÕES DE ALUNOS
    // ============================================================

    if ($action === 'create') {
        $result = $studentService->createStudent(
            $_POST['name'], 
            $_POST['email'], 
            $_POST['password'], 
            $adminId, 
            $token
        );

        if (isset($result['success'])) {
            header("Location: admins-students.page.php?success=created");
        } else {
            $msg = $result['msg'] ?? 'Erro desconhecido';
            header("Location: admins-students.page.php?error=" . ($result['error'] ?? 'create_failed') . "&msg=" . urlencode($msg));
        }

    } elseif ($action === 'delete') {
        $success = $studentService->deleteStudent($_POST['user_id'], $token);
        
        if ($success) header("Location: admins-students.page.php?success=deleted");
        else header("Location: admins-students.page.php?error=delete_failed");

    } elseif ($action === 'update_student_class') {
        $classId = (!empty($_POST['class_id']) && $_POST['class_id'] !== 'none') ? $_POST['class_id'] : null;
        $success = $studentService->changeClass($_POST['student_id'], $classId, $token);

        if ($success) header("Location: admins-students.page.php?success=class_updated");
        else header("Location: admins-students.page.php?error=update_failed");

    // ============================================================
    // AÇÕES DE SALAS
    // ============================================================

    } elseif ($action === 'create_class') {
        $className = $_POST['class_name'] ?? '';
        
        if (empty($className)) {
            header("Location: admins-students.page.php?error=empty_name");
            exit;
        }

        $institutionId = $studentService->getInstitutionId($adminId, $token);

        if ($institutionId) {
            $data = [
                'class_name' => $className,
                'institution_id' => $institutionId
            ];
            
            // Usa Service Key para garantir permissão
            $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "classes", "POST", $data, null);

            if (isSupabaseError($res)) {
                header("Location: admins-students.page.php?error=class_create_failed");
            } else {
                header("Location: admins-students.page.php?success=class_created");
            }
        } else {
            header("Location: admins-students.page.php?error=institution_not_found");
        }

    } elseif ($action === 'delete_class') {
        $classId = $_POST['class_id'];

        // Desvincula alunos
        \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "students?class_id=eq.$classId", "PATCH", ['class_id' => null], null);

        // Apaga sala
        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "classes?id=eq.$classId", "DELETE", null, null);

        if (isSupabaseError($res)) {
            header("Location: admins-students.page.php?error=class_delete_failed");
        } else {
            header("Location: admins-students.page.php?success=class_deleted");
        }

    } else {
        header("Location: admins-students.page.php");
    }
    
} catch (Exception $e) {
    header("Location: admins-students.page.php?error=exception&msg=" . urlencode($e->getMessage()));
}
exit;
?>