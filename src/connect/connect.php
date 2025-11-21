<?php
$supabaseUrl = "https://ffcrtnubzhtyqnzfyfee.supabase.co";
$supabaseKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZmY3J0bnViemh0eXFuemZ5ZmVlIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTQxMjcyMDUsImV4cCI6MjA2OTcwMzIwNX0.SIG-uUW03FDFxDfc5V4YOGhNx4QI9zfj8HFdjEgrmYg";

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

$supabaseServiceKey = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImZmY3J0bnViemh0eXFuemZ5ZmVlIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc1NDEyNzIwNSwiZXhwIjoyMDY5NzAzMjA1fQ.9DsR5EcxXGp_u-2OmamOInFPiZzJbGXaDgyU5g2DPTY";

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