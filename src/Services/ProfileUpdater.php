<?php

namespace Blome\Services;

class ProfileUpdater
{
    public function __construct(
        protected SupabaseClientInterface $supabaseClient
    ) {}

    /**
     * Atualiza dados de perfil na tabela REST correspondente.
     */
    public function updateProfile(string $userId, string $role, string $token, array $data): ?array
    {
        $endpoint = $this->getEndpointForRole($userId, $role);

        if ($endpoint === null) {
            return null;
        }

        return $this->safeCall(fn() =>
            $this->supabaseClient->patch($endpoint, $data, $token)
        );
    }

    /**
     * Atualiza dados de autenticação.
     */
    public function updateUserAuth(string $token, array $data): ?array
    {
        return $this->safeCall(fn() =>
            $this->supabaseClient->updateAuth($data, $token)
        );
    }

    /**
     * [IMPORTANTE] Essa função estava faltando e causava o erro Fatal.
     */
    private function getEndpointForRole(string $userId, string $role): ?string
    {
        $tables = [
            'admin'     => 'admins_info',
            'professor' => 'professors_info',
            'student'   => 'students',
        ];

        if (!isset($tables[$role])) {
            return null;
        }

        return sprintf('%s?id=eq.%s', $tables[$role], $userId);
    }

    /**
     * [IMPORTANTE] Essa função também estava faltando.
     */
    private function safeCall(callable $operation): ?array
    {
        try {
            return $operation();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Atualiza o avatar gerando um nome novo para evitar Cache.
     * NÃO deleta a imagem antiga (para simplificar).
     */
    public function updateAvatar(
        string $userId, 
        string $role, 
        string $token, 
        array $fileData, 
        string $bucketName 
    ): ?array {

        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // 1. Gera nome aleatório para garantir que a imagem troque na hora (sem cache)
        $folder = $role; 
        if ($folder === null) return null;

        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        // Ex: a1b2c3_170999.png
        $newFileName = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        
        $bucketPath = $folder . '/' . $newFileName;
        $fullBucketPath = $bucketName . '/' . $bucketPath;

        // 2. Faz o Upload
        $uploadResult = $this->supabaseClient->uploadToBucket(
            $fullBucketPath,
            $fileData['tmp_name'],
            $fileData['type'],
            $token
        );

        if ($uploadResult === null || isset($uploadResult['error'])) {
            return null;
        }

        // 3. Atualiza o Banco com a nova URL
        $storageBaseUrl = "https://ffcrtnubzhtyqnzfyfee.supabase.co/storage/v1/object/public";
        $publicUrl = $storageBaseUrl . '/' . $fullBucketPath;
        $dataToUpdate = ['avatar_url' => $publicUrl];
        
        return $this->updateProfile($userId, $role, $token, $dataToUpdate);
    }
}