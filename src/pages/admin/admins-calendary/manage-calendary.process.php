<?php
session_start();
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../Services/CalendarService.php';

// --- Verificações de Segurança ---
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido.");
}
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

use Blome\Services\CalendarService;

// Instancia o serviço
$calendarService = new CalendarService($client);

$action = $_POST['action'] ?? '';
$classId = $_POST['class_id'] ?? '';
$redirectBase = "admins-calendary.page.php?class_filter=" . $classId;

// Vamos usar a Service Key para garantir acesso total aos dados, 
// ignorando restrições de RLS que podem estar bloqueando o token do usuário.
global $supabaseServiceKey; 

try {
    if ($action === 'create' || $action === 'update') {
        $profId = $_POST['professor_id'];
        $subjectId = $_POST['subject_id'];
        $dayOfWeek = $_POST['day_of_week'];
        $startTime = $_POST['start_time'];
        $endTime = $_POST['end_time'];
        $scheduleId = $_POST['schedule_id'] ?? null;

        // 1. Validação Básica
        if ($startTime >= $endTime) {
            header("Location: $redirectBase&error=" . urlencode("Horário final deve ser maior que o inicial."));
            exit;
        }

        // 2. Verifica Conflitos
        // CORREÇÃO: Usamos $supabaseServiceKey em vez de $token
        $ignoreId = ($action === 'update') ? $scheduleId : null;
        $conflict = $calendarService->checkConflict($dayOfWeek, $startTime, $endTime, $classId, $profId, $supabaseServiceKey, $ignoreId);
        
        if ($conflict) {
            header("Location: $redirectBase&error=" . urlencode($conflict));
            exit;
        }

        // 3. Busca ID da Relação
        // CORREÇÃO: Usamos $supabaseServiceKey para garantir que o backend encontre o vínculo
        $profSubId = $calendarService->getProfessorSubjectId($profId, $subjectId, $supabaseServiceKey);
        
        if (!$profSubId) {
            header("Location: $redirectBase&error=" . urlencode("Professor não vinculado a esta matéria (Erro de Permissão ou Vínculo inexistente)."));
            exit;
        }

        $data = [
            'professor_id' => $profId,
            'class_id' => $classId,
            'subject_id' => $subjectId,
            'professors_subjects_id' => $profSubId,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime
        ];

        // Para criar/atualizar, o service já usa a Service Key internamente ou methods globais, 
        // mas vamos passar os dados corretamente.
        if ($action === 'create') {
            $res = $calendarService->createLesson($data);
        } else {
            $res = $calendarService->updateLesson($scheduleId, $data);
        }

        if (isset($res['success'])) {
            header("Location: $redirectBase&success=saved");
        } else {
            header("Location: $redirectBase&error=" . urlencode($res['error']));
        }

    } elseif ($action === 'delete') {
        $success = $calendarService->deleteLesson($_POST['schedule_id']);
        
        if ($success) header("Location: $redirectBase&success=deleted");
        else header("Location: $redirectBase&error=delete_failed");
    } else {
        header("Location: admins-calendary.page.php");
    }

} catch (Exception $e) {
    header("Location: admins-calendary.page.php?error=exception");
}
exit;
?>