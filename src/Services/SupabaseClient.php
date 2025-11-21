<?php

namespace Blome\Services;

use Blome\Services\SupabaseClientInterface;

class SupabaseClient implements SupabaseClientInterface
{

    private string $supabaseUrl;
    private string $supabaseKey;

    public function __construct(string $supabaseUrl, string $supabaseKey)
    {
        $this->supabaseUrl = $supabaseUrl;
        $this->supabaseKey = $supabaseKey;
    }

    public function fetch(string $endpoint, string $accessToken): ?array
    {
        return \supabaseRestRequest(
            $this->supabaseUrl, 
            $this->supabaseKey,
            $endpoint,
            'GET',
            null,
            $accessToken);
    }

    public function patch(string $endpoint, array $data, string $accessToken): ?array
    {
        return \supabaseRestRequest(
            $this->supabaseUrl, 
            $this->supabaseKey,
            $endpoint,
            'PATCH',
            $data,
            $accessToken);
    }

    public function updateAuth(array $data, string $accessToken): ?array
    {
        return \supabaseAuthRequest(
            'user',
            'PUT',
            $data,
            $accessToken
        );
    }

    public function uploadToBucket(string $bucketPath, string $fileTempPath, string $fileType, string $accessToken): ?array
    {
        return \supabaseStorageRequest(
            $this->supabaseUrl,
            $this->supabaseKey,
            $bucketPath,
            $fileTempPath,
            $fileType,
            $accessToken
        );
    }

    // [NOVO] Implementação da deleção correta
    public function deleteFromBucket(string $bucketPath, string $accessToken): ?array
    {
        // Endpoint de DELETE: /storage/v1/object/{bucket}/{caminho_arquivo}
        // Repare que NÃO tem '/public' aqui.
        $url = $this->supabaseUrl . "/storage/v1/object/" . $bucketPath;

        $headers = [
            "apikey: " . $this->supabaseKey,
            "Authorization: Bearer " . $accessToken,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Verbo HTTP DELETE
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Ignorar SSL localmente
        if (in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1'])) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ["error" => $error];
        }

        return json_decode($response, true);
    }
}