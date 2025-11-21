<?php
namespace Blome\Services;

class ProfileUpdateHandler
{
    private $updater;
    private $session;
    private $post;
    private $files;
    private $server;

    public function __construct(
        ProfileUpdater $updater, 
        array $session, 
        array $post, 
        array $files,
        array $server
    ) {
        $this->updater = $updater;
        $this->session = $session;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
    }

    public function handleRequest(): string
    {
        if (!isset($this->session['access_token']) || !isset($this->session['user_id'])) {
            return "../login/login.page.php?error=not_logged_in";
        }

        $userId = $this->session['user_id'];
        $role = $this->session['user_role'];
        $token = $this->session['access_token'];

        $authChanged = false;


        // --- ATUALIZAR NOME ---
        if (!empty($this->post['full_name'])) {
            $data = ['full_name' => $this->post['full_name']];
            $this->updater->updateProfile($userId, $role, $token, $data);
        }

        // --- ATUALIZAR E-MAIL (Auth) ---
        if (!empty($this->post['new_email'])) {
            $data = ['email' => $this->post['new_email']];
            $this->updater->updateUserAuth($token, $data);
            $authChanged = true;
        }

        // --- ATUALIZAR SENHA (Auth) ---
        if (!empty($this->post['new_password'])) {
            if (strlen($this->post['new_password']) < 6) {
                $redirectUrl = $this->server['HTTP_REFERER'] ?? '../../pages/login/login.page.php';
                return strtok($redirectUrl, '?') . "?error=password_too_short";
            }
            $data = ['password' => $this->post['new_password']];
            $this->updater->updateUserAuth($token, $data);
            $authChanged = true; 
        }

        // --- ATUALIZAR AVATAR ---
        if (isset($this->files['avatar_file']) && $this->files['avatar_file']['error'] == 0) {
            $this->updater->updateAvatar($userId, $role, $token, $this->files['avatar_file'], 'users-icons');
        }

        if ($authChanged) {
            return "../../pages/login/login.page.php?success=auth_updated";
        } else {
            $redirectUrl = $this->server['HTTP_REFERER'] ?? '../../pages/login/login.page.php';
            $redirectUrl = strtok($redirectUrl, '?'); 
            return $redirectUrl . "?success=true";
        }
    }
}