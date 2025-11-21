<?php

namespace Blome\Services;

class ProfessorService
{
    private SupabaseClientInterface $client;

    public function __construct(SupabaseClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Busca o ID da instituição do Admin (Reutilizável)
     */
    public function getInstitutionId(string $adminId, string $token): ?string
    {
        $data = $this->client->fetch("admins_info?select=institution_id&id=eq.$adminId", $token);
        if (!empty($data) && isset($data[0]['institution_id'])) {
            return $data[0]['institution_id'];
        }
        $dataBackup = $this->client->fetch("admins_info?select=institutions_id&id=eq.$adminId", $token);
        return $dataBackup[0]['institutions_id'] ?? null;
    }

    /**
     * Cria Professor (Auth + DB)
     */
    public function createProfessor(string $name, string $email, string $password, string $adminId, string $token): array
    {
        $institutionId = $this->getInstitutionId($adminId, $token);
        if (!$institutionId) return ['error' => 'institution_not_found'];

        // 1. Criar no Auth (Supabase)
        $authResponse = \supabaseAdminAuthRequest("users", "POST", [
            "email" => $email,
            "password" => $password,
            "email_confirm" => true
        ]);

        if (isset($authResponse['error']) || !isset($authResponse['id'])) {
            return ['error' => 'auth_failed', 'msg' => json_encode($authResponse)];
        }

        $newUserId = $authResponse['id'];

        // 2. Inserir na tabela professors_info
        // Usamos a ServiceKey (Admin Supremo) para garantir permissão de escrita
        global $supabaseUrl, $supabaseServiceKey;
        
        $data = [
            'id' => $newUserId,
            'full_name' => $name,
            'institution_id' => $institutionId,
            'avatar_url' => 'https://placehold.co/150'
        ];

        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_info", "POST", $data, null);

        if ($this->isError($res)) {
            // Rollback: deleta do Auth se falhar no banco
            \supabaseAdminAuthRequest("users/$newUserId", "DELETE");
            return ['error' => 'db_failed'];
        }

        return ['success' => true];
    }

    /**
     * Deleta Professor e suas relações
     */
    public function deleteProfessor(string $userId): bool
    {
        global $supabaseUrl, $supabaseServiceKey;

        // 1. Deleta matérias vinculadas
        \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects?professor_id=eq.$userId", "DELETE", null, null);

        // 2. Deleta info do professor
        \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_info?id=eq.$userId", "DELETE", null, null);

        // 3. Deleta login
        $authRes = \supabaseAdminAuthRequest("users/$userId", "DELETE");

        return !isset($authRes['error']);
    }

    /**
     * Atualiza as matérias que o professor dá aula
     */
    public function updateSubjects(string $professorId, array $subjectIds): bool
    {
        global $supabaseUrl, $supabaseServiceKey;

        // 1. Limpa as atuais
        \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects?professor_id=eq.$professorId", "DELETE", null, null);

        if (empty($subjectIds)) return true;

        // 2. Insere as novas
        $insertBatch = [];
        foreach ($subjectIds as $subId) {
            $insertBatch[] = ['professor_id' => $professorId, 'subject_id' => (int)$subId];
        }

        $res = \supabaseRestRequest($supabaseUrl, $supabaseServiceKey, "professors_subjects", "POST", $insertBatch, null);

        return !$this->isError($res);
    }

    /**
     * Helper para checar erro (incluindo o caso null = sucesso)
     */
    private function isError($response): bool
    {
        if ($response === null) return false;
        if (isset($response['error'])) return true;
        if (isset($response['code']) && strpos($response['code'], '2') !== 0) return true;
        return false;
    }
}
?>