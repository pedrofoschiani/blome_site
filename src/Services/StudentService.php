<?php

namespace Blome\Services;

class StudentService
{
    private SupabaseClientInterface $client;

    public function __construct(SupabaseClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Busca o ID da instituição do Admin
     */
    public function getInstitutionId(string $adminId, string $token): ?string
    {
        // Tenta buscar o admin e sua instituição
        $data = $this->client->fetch("admins_info?select=institution_id&id=eq.$adminId", $token);
        
        if (!empty($data) && isset($data[0]['institution_id'])) {
            return $data[0]['institution_id'];
        }
        
        // Fallback: Tenta verificar se a coluna se chama 'institutions_id' (caso mude no banco)
        $dataBackup = $this->client->fetch("admins_info?select=institutions_id&id=eq.$adminId", $token);
        return $dataBackup[0]['institutions_id'] ?? null;
    }

    /**
     * Cria um novo aluno (Auth + Tabela Students)
     */
    public function createStudent(string $name, string $email, string $password, string $adminId, string $token): array
    {
        $institutionId = $this->getInstitutionId($adminId, $token);

        if (!$institutionId) {
            return ['error' => 'institution_not_found'];
        }

        // 1. Cria usuário no Auth (Supabase)
        // Nota: A função updateAuth do seu cliente é PUT, precisamos de uma create (POST).
        // Como o seu SupabaseClient.php atual é simples, vamos usar a função global temporariamente 
        // ou idealmente adicionar 'adminCreateUser' no SupabaseClient.
        
        // Usando a função global existente para Auth Admin
        $authResponse = \supabaseAdminAuthRequest("users", "POST", [
            "email" => $email,
            "password" => $password,
            "email_confirm" => true
        ]);

        if (isset($authResponse['error']) || !isset($authResponse['id'])) {
            return ['error' => 'auth_failed', 'msg' => json_encode($authResponse)];
        }

        $newUserId = $authResponse['id'];

        // 2. Insere na tabela students
        $data = [
            'id' => $newUserId,
            'full_name' => $name,
            'institutions' => $institutionId, // Atenção ao nome da coluna no seu banco
            'avatar_url' => 'https://placehold.co/150'
        ];

        $response = $this->client->patch("students", $data, $token); // O cliente atual usa PATCH, mas precisamos POST.

        // Vamos usar a função global supabaseRestRequest aqui dentro por enquanto 
        // pois o seu SupabaseClient->fetch é só GET e patch é PATCH.
        // Numa otimização futura, adicionaria o método post() no SupabaseClient.
        
        global $supabaseUrl, $supabaseKey; // Hack temporário até melhorar o Client
        $insertResponse = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students", "POST", $data, $token);

        if (isset($insertResponse['error'])) {
            // Rollback: deleta o usuário se falhar a tabela
            \supabaseAdminAuthRequest("users/$newUserId", "DELETE");
            return ['error' => 'db_failed'];
        }

        return ['success' => true];
    }

    public function deleteStudent(string $userId, string $token): bool
    {
        // Deleta da tabela
        global $supabaseUrl, $supabaseKey;
        $res = \supabaseRestRequest($supabaseUrl, $supabaseKey, "students?id=eq.$userId", "DELETE", null, $token);
        
        if (isset($res['error'])) return false;

        // Deleta do Auth
        \supabaseAdminAuthRequest("users/$userId", "DELETE");
        return true;
    }
    
    public function changeClass(string $studentId, ?string $classId, string $token): bool
    {
        $data = ['class_id' => $classId];
        $res = $this->client->patch("students?id=eq.$studentId", $data, $token);
        return !isset($res['error']);
    }
}
?>