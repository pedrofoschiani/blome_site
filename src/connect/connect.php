<?php
$configFile = __DIR__ . '/../config.php';

if (!file_exists($configFile)) {
    // Se o arquivo não existir, mata o processo com um aviso claro
    die("Erro Crítico: O arquivo de configuração 'src/config.php' não foi encontrado. <br> Renomeie o 'src/config.example.php' e adicione suas chaves.");
}

$config = require($configFile);

if (!is_array($config)) {
    die("Erro Crítico: O arquivo 'src/config.php' existe mas não retornou um array. Verifique se ele começa com 'return [ ... ]'.");
}

$supabaseUrl = $config['SUPABASE_URL'];
$supabaseKey = $config['SUPABASE_KEY'];
$supabaseServiceKey = $config['SUPABASE_SERVICE_KEY'] ?? '';

if (!$supabaseUrl || !$supabaseKey) {
    die("Erro de Configuração: As chaves SUPABASE_URL ou SUPABASE_KEY estão vazias no arquivo config.php.");
}

$isLocal = in_array($_SERVER['SERVER_NAME'], ['localhost', '127.0.0.1']);

function supabaseAuthRequest($endpoint, $method = 'POST', $data = null, $accessToken = null) {
    global $supabaseUrl, $supabaseKey, $isLocal;

    $token = $accessToken ?: $supabaseKey;

    $headers = [
        "apikey: $supabaseKey",
        "Authorization: Bearer $token",
        "Content-Type: application/json",
    ];

    $options = [
        CURLOPT_URL => $supabaseUrl . "/auth/v1/" . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
    ];

    if ($data) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }
    
    if($isLocal) {
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ["error" => $error];
    }

    return json_decode($response, true);
}

function supabaseRestRequest(
    string $supabaseUrl,     
    string $supabaseKey,
    string $endpoint, 
    $method = 'GET', 
    $data = null, 
    $accessToken = null
) {
    // global $supabaseUrl, $supabaseKey; // <-- REMOVA ESTA LINHA
    global $isLocal; // Mantenha esta se você usa $isLocal

    $token = $accessToken ?: $supabaseKey;

    $headers = [
        "apikey: $supabaseKey",
        "Authorization: Bearer $token",
        "Content-Type: application/json",
        "Accept: application/json"
    ];

    $options = [
        CURLOPT_URL => $supabaseUrl . "/rest/v1/" . $endpoint, 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
    ];

    if ($data) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }
    
    if(!empty($isLocal)) {
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ["error" => $error];
    }

    return json_decode($response, true);
}

function supabaseStorageRequest(
    string $supabaseUrl,
    string $supabaseKey,
    string $bucketPath,     
    string $fileTempPath,  
    string $fileType,
    string $accessToken
) {
    global $isLocal;

    // O endpoint do Storage é diferente 
    $url = $supabaseUrl . "/storage/v1/object/" . $bucketPath;
    
    $fileData = new \CURLFile($fileTempPath, $fileType);

    $headers = [
        "apikey: $supabaseKey",
        "Authorization: Bearer $accessToken",
        "Content-Type: multipart/form-data",
        "x-upsert: true"
    ];

    $postData = [
        'file' => $fileData
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
    ];

    if ($isLocal) {
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ["error" => $error];
    }
    return json_decode($response, true);
}

function supabaseAdminAuthRequest($endpoint, $method = 'GET', $data = null) {
    global $supabaseUrl, $supabaseServiceKey, $isLocal;

    $url = $supabaseUrl . "/auth/v1/admin/" . $endpoint; // Endpoint de Admin

    $headers = [
        "apikey: $supabaseServiceKey",
        "Authorization: Bearer $supabaseServiceKey", // Usa a Service Key
        "Content-Type: application/json",
    ];

    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_CUSTOMREQUEST => $method,
    ];

    if ($data) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }
    
    if($isLocal) {
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
    }

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) { return ["error" => $error]; }
    return json_decode($response, true);
}
?>