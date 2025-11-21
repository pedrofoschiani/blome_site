<?php

namespace Blome\Services;

class AppService
{
    private SupabaseClientInterface $client;

    public function __construct(SupabaseClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Desbloqueia um App para a escola (Chama a função RPC do Banco)
     */
    public function unlockApp(string $adminId, string $appId, string $token): array
    {
        $data = [
            'p_admin_id' => $adminId,
            'p_app_id'   => $appId
        ];

        // RPC (Remote Procedure Call)
        $response = \supabaseRestRequest(
            global_supabase_url(),
            global_supabase_key(),
            "rpc/unlock_app_global_and_clean", 
            "POST", 
            $data, 
            $token
        );

        // --- CORREÇÃO DO ERRO ---
        // Se o Supabase retornar apenas uma string (ex: "OK") ou null, 
        // transformamos num array para não quebrar o tipo de retorno.
        if (!is_array($response)) {
            return ['message' => $response, 'success' => true];
        }

        return $response;
    }

    /**
     * Bloqueia o App novamente (Remove da tabela admins_apps)
     */
    public function lockApp(string $adminId, string $appId, string $token): bool
    {
        $query = "admins_apps?admin_id=eq.$adminId&app_id=eq.$appId";
        
        // Para garantir consistência, usamos a função global
        global $supabaseUrl, $supabaseKey;
        $response = \supabaseRestRequest($supabaseUrl, $supabaseKey, $query, "DELETE", null, $token);

        // Se for array e tiver erro, falhou. Se for null ou outra coisa, assumimos sucesso no delete.
        if (is_array($response) && isset($response['error'])) {
            return false;
        }
        
        return true;
    }
}

// Helpers locais para garantir acesso às variáveis globais
function global_supabase_url() { global $supabaseUrl; return $supabaseUrl; }
function global_supabase_key() { global $supabaseKey; return $supabaseKey; }
?>