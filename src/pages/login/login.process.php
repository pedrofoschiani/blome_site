<?php
session_start();

include('../../connect/connect.php');

require_once __DIR__ . '/../../init.php';

if (!isset($_POST['csrf_token'])) {
    die("Erro de segurança: Formulário inválido (Token ausente).");
}

if (!isset($_SESSION['csrf_token'])) {
    die("Sessão expirada. Por favor, atualize a página de login e tente novamente.");
}

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Erro de segurança: Token inválido. Atualize a página.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $login = supabaseAuthRequest("token?grant_type=password", "POST", [
        "email" => $email,
        "password" => $password
    ]);

    if (isset($login["error"]) || !isset($login["access_token"])) {
        header("Location: login.page.php?error=invalid_credentials");
        exit;
    }
    
    $accessToken = $login["access_token"];
    $userId = $login["user"]["id"];
    $userRole = null; 
    
    $admin = supabaseRestRequest($supabaseUrl, $supabaseKey, "admins_info?select=id&id=eq.$userId", "GET", null, $accessToken);
    if (!empty($admin)) {
        $userRole = 'admin';
    } else {
        $professor = supabaseRestRequest($supabaseUrl, $supabaseKey, "professors_info?select=id&id=eq.$userId", "GET", null, $accessToken);
        if (!empty($professor)) {
            $userRole = 'professor';
        } else {
            $student = supabaseRestRequest($supabaseUrl, $supabaseKey, "students?select=id&id=eq.$userId", "GET", null, $accessToken);
            if (!empty($student)) {
                $userRole = 'student';
            }
        }
    }

    if ($userRole) {
        $_SESSION['access_token'] = $accessToken;
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_role'] = $userRole;

        switch ($userRole) {
            case 'admin':
                header("Location: ../admin/admins-home/admins-home.page.php");
                break;
            case 'professor':
                header("Location: ../professor/professors-home/professors-home.page.php");
                break;
            case 'student':
                header("Location: ../students/students-home/students-home.page.php");
                break;
        }
        exit;
    } else {
        header("Location: login.page.php?error=no_profile");
        exit;
    }
}