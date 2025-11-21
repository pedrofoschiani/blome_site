<?php
session_start();
require_once(__DIR__ . '/../../../connect/connect.php');

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido ou expirado. Atualize a página e tente novamente.");
}

// Validação de Acesso
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado.");
}

$action = $_POST['action'] ?? '';
$redirectBase = "admins-calendary.page.php";

// --- HELPER FUNCTION 1: CHECK CONFLICTS ---
function checkScheduleConflict($supabaseUrl, $serviceKey, $day, $start, $end, $classId, $profId, $ignoreId = null) {
    
    // 1. Verifica conflito da TURMA
    $queryClass = "professors_schedule?class_id=eq.$classId&day_of_week=eq.$day";
    $queryClass .= "&start_time=lt.$end&end_time=gt.$start";
    
    if ($ignoreId) {
        $queryClass .= "&id=neq.$ignoreId";
    }

    $conflictClass = supabaseRestRequest($supabaseUrl, $serviceKey, $queryClass, "GET", null, null);

    if (!empty($conflictClass) && !isset($conflictClass['error'])) {
        return "Esta turma já tem aula neste horário (" . $conflictClass[0]['start_time'] . " - " . $conflictClass[0]['end_time'] . ").";
    }

    // 2. Verifica conflito do PROFESSOR
    $queryProf = "professors_schedule?professor_id=eq.$profId&day_of_week=eq.$day";
    $queryProf .= "&start_time=lt.$end&end_time=gt.$start";
    
    if ($ignoreId) {
        $queryProf .= "&id=neq.$ignoreId";
    }

    $conflictProf = supabaseRestRequest($supabaseUrl, $serviceKey, $queryProf, "GET", null, null);

    if (!empty($conflictProf) && !isset($conflictProf['error'])) {
        return "O professor selecionado já está ocupado neste horário em outra turma.";
    }

    return null; // Sem conflitos
}

// --- HELPER FUNCTION 2: GET RELATION ID (NEW) ---
function getProfessorSubjectId($supabaseUrl, $serviceKey, $profId, $subjectId) {
    // Busca o ID da relação na tabela professors_subjects
    $query = "professors_subjects?select=id&professor_id=eq.$profId&subject_id=eq.$subjectId";
    $result = supabaseRestRequest($supabaseUrl, $serviceKey, $query, "GET", null, null);

    if (!empty($result) && !isset($result['error']) && isset($result[0]['id'])) {
        return $result[0]['id'];
    }
    return null;
}

// --- MAIN LOGIC ---

if ($action === 'create' || $action === 'update') {
    $classId = $_POST['class_id'];
    $profId = $_POST['professor_id'];
    $subjectId = $_POST['subject_id'];
    $dayOfWeek = $_POST['day_of_week'];
    $startTime = $_POST['start_time'];
    $endTime = $_POST['end_time'];
    $scheduleId = $_POST['schedule_id'] ?? null;

    $redirectUrl = $redirectBase . "?class_filter=" . $classId;

    // 1. Basic Validations
    if ($startTime >= $endTime) {
        header("Location: $redirectUrl&error=" . urlencode("O horário de término deve ser maior que o de início."));
        exit;
    }

    // 2. Conflict Validation
    $conflictError = checkScheduleConflict($supabaseUrl, $supabaseServiceKey, $dayOfWeek, $startTime, $endTime, $classId, $profId, $scheduleId);
    
    if ($conflictError) {
        header("Location: $redirectUrl&error=" . urlencode($conflictError));
        exit;
    }

    // 3. Get Professor_Subject ID (Fix for the missing column)
    $profSubId = getProfessorSubjectId($supabaseUrl, $supabaseServiceKey, $profId, $subjectId);

    if (!$profSubId) {
        header("Location: $redirectUrl&error=" . urlencode("Erro: Este professor não está vinculado a esta matéria na tabela de relações."));
        exit;
    }

    // 4. Prepare Data
    $data = [
        'professor_id' => $profId,
        'class_id' => $classId,
        'subject_id' => $subjectId,
        'professors_subjects_id' => $profSubId, // <--- Added here
        'day_of_week' => $dayOfWeek,
        'start_time' => $startTime,
        'end_time' => $endTime
    ];

    // 5. Execute Request
    if ($action === 'create') {
        $response = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_schedule", "POST", $data, null);
    } else {
        $response = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_schedule?id=eq.$scheduleId", "PATCH", $data, null);
    }

    if (isset($response['error'])) {
        $msg = $response['error']['message'] ?? "Erro desconhecido";
        header("Location: $redirectUrl&error=" . urlencode("Erro ao salvar: " . $msg));
    } else {
        header("Location: $redirectUrl&success=saved");
    }
    exit;

} elseif ($action === 'delete') {
    $scheduleId = $_POST['schedule_id'];
    $classId = $_POST['class_id'];

    $response = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_schedule?id=eq.$scheduleId", "DELETE", null, null);
    
    $redirectUrl = $redirectBase . "?class_filter=" . $classId;
    
    if (isset($response['error'])) {
        header("Location: $redirectUrl&error=delete_failed");
    } else {
        header("Location: $redirectUrl&success=deleted");
    }
    exit;
}

header("Location: $redirectBase");
exit;
?>