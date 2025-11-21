<?php
namespace Blome\Services;

use Blome\Services\UserProvider;

class UserProfileLoader
{
    private $userProvider;
    private $session;
    private $role;
    private $userId;
    private $token;

    public function __construct(UserProvider $userProvider, array $session)
    {
        $this->userProvider = $userProvider;
        $this->session = $session;

        $this->role = $this->session['user_role'] ?? null;
        $this->userId = $this->session['user_id'] ?? null;
        $this->token = $this->session['access_token'] ?? null;
    }

    public function getSessionRole(): ?string
    {
        return $this->role;
    }

    public function getProfileData(): ?array
    {
        //Garantir a existência dos dados necessários
        if ($this->role === null || $this->userId === null || $this->token === null) {
            return null;
        }

        // Tenta buscar o perfil
        $profile = $this->userProvider->getUserProfile(
            $this->userId,
            $this->role,
            $this->token
        );

        // Se o UserProvider falhar, $profile será null
        if ($profile === null) {
            // Se falhar, retorne os defaults PARA AQUELE ROLE
            return $profile = $this->getDefaults();
        }

        return $profile;
    }

    /**
     * Retorna os padrões para o role
     */
    private function getDefaults(): array
    {
        switch ($this->role) {
            case 'admin':
                $defaultName = 'Administrador';
                break;
            case 'professor':
                $defaultName = 'Professor(a)';
                break;
            case 'student':
                $defaultName = 'Aluno(a)';
                break;
        }

        return [
            'full_name' => $defaultName,
            'avatar_url' => '../../../assets/logo/logoB_A.png'
        ];
    }
}