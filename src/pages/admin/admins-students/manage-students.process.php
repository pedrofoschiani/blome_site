<?php
session_start();
require_once(__DIR__ . '/../../../connect/connect.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Acesso negado. Você não é admin.");
}

$action = $_POST['action'] ?? '';

// Função auxiliar robusta para pegar o ID da instituição
function getAdminInstitutionId($supabaseUrl, $supabaseServiceKey, $adminId) {
    // Tenta as 3 variações comuns de nome de coluna que vimos nos seus prints
    $endpoints = [
        "admins_info?select=institutions_id&id=eq.$adminId", // Padrão 1
        "admins_info?select=institution_id&id=eq.$adminId",  // Padrão 2
        "admins_info?select=institutions&id=eq.$adminId"     // Padrão 3 (visto na tabela students)
    ];

    foreach ($endpoints as $ep) {
        $data = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, $ep);
        if (!empty($data) && !isset($data['error']) && isset($data[0])) {
            // Retorna o primeiro valor que não seja nulo
            foreach ($data[0] as $key => $val) {
                if ($val) return $val;
            }
        }
    }
    return null;
}

// --- ALUNO: CRIAR ---
if ($action === 'create') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $adminId = $_SESSION['user_id'];
    $institutionId = getAdminInstitutionId($supabaseUrl, $supabaseServiceKey, $adminId);

    if (!$institutionId) {
        die("Erro Crítico: Não foi possível encontrar a instituição do Admin. Verifique a tabela admins_info.");
    }

    // Cria no Auth
    $authResponse = supabaseAdminAuthRequest("users", "POST", [
        "email" => $email,
        "password" => $password,
        "email_confirm" => true
    ]);

    if (isset($authResponse['error']) || !isset($authResponse['id'])) {
        die("Erro ao criar no Auth: " . json_encode($authResponse));
    }

    $newUserId = $authResponse['id'];

    // Insere na tabela STUDENTS
    // NOTA: Usando 'institutions' baseado no seu print da tabela students
    $insertData = [
        'id' => $newUserId,
        'full_name' => $name,
        'institutions' => $institutionId, 
        'avatar_url' => 'https://placehold.co/150'
    ];

    $insertResponse = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "students", "POST", $insertData);

    if (isset($insertResponse['error'])) {
        supabaseAdminAuthRequest("users/$newUserId", "DELETE");
        die("Erro ao salvar na tabela students: " . json_encode($insertResponse));
    }

    header("Location: admins-students.page.php?success=created");
    exit;

// --- ALUNO: DELETAR ---
} elseif ($action === 'delete') {
    $userId = $_POST['user_id'];

    $tableDeleteResponse = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "students?id=eq.$userId", "DELETE");

    if (isset($tableDeleteResponse['error'])) {
        header("Location: admins-students.page.php?error=delete_failed");
        exit;
    }

    supabaseAdminAuthRequest("users/$userId", "DELETE");
    header("Location: admins-students.page.php?success=deleted");
    exit;

// --- SALA: CRIAR ---
} elseif ($action === 'create_class') {
    $className = $_POST['class_name'];
    
    if (empty($className)) {
        header("Location: admins-students.page.php?error=empty_name");
        exit;
    }

    $adminId = $_SESSION['user_id'];
    $institutionId = getAdminInstitutionId($supabaseUrl, $supabaseServiceKey, $adminId);

    if ($institutionId) {
        // NOTA: Usando 'institution_id' baseado no seu print da tabela classes
        $insertData = [
            'class_name' => $className,
            'institution_id' => $institutionId
        ];
        
        $response = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "classes", "POST", $insertData);

        if (isset($response['error'])) {
            $errorMsg = urlencode($response['error']['message'] ?? 'unknown');
            header("Location: admins-students.page.php?error=class_create_failed&msg=$errorMsg");
        } else {
            header("Location: admins-students.page.php?success=class_created");
        }
    } else {
        header("Location: admins-students.page.php?error=admin_institution_not_found");
    }
    exit;

// --- SALA: DELETAR ---
} elseif ($action === 'delete_class') {
    $classId = $_POST['class_id'];

    // Desvincula alunos
    supabaseRestRequest(
        $supabaseUrl, 
        $supabaseServiceKey, 
        "students?class_id=eq.$classId", 
        "PATCH", 
        ['class_id' => null]
    );

    $response = supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "classes?id=eq.$classId", "DELETE");

    if (isset($response['error'])) {
        header("Location: admins-students.page.php?error=class_delete_failed");
    } else {
        header("Location: admins-students.page.php?success=class_deleted");
    }
    exit;

// --- ALUNO: MUDAR SALA ---
} elseif ($action === 'update_student_class') {
    $studentId = $_POST['student_id'];
    $classId = (!empty($_POST['class_id']) && $_POST['class_id'] !== 'none') ? $_POST['class_id'] : null;

    $data = ['class_id' => $classId];
    
    $response = supabaseRestRequest(
        $supabaseUrl, 
        $supabaseServiceKey, 
        "students?id=eq.$studentId", 
        "PATCH", 
        $data
    );

    if (isset($response['error'])) {
        header("Location: admins-students.page.php?error=update_failed");
    } else {
        header("Location: admins-students.page.php?success=class_updated");
    }
    exit;
}
?>