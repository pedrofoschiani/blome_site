<?php

namespace Blome\Services;

interface SupabaseClientInterface
{
    public function fetch(string $endpoint, string $accessToken): ?array;

    //Teste 8 ao 10 dependem deste método
    public function patch(string $endpoint, array $data, string $accessToken): ?array;

    //Teste 11 depende deste método
    public function updateAuth(array $data, string $accessToken): ?array;

    //Teste 13 depende deste método
    public function uploadToBucket(string $bucketPath, string $fileTempPath, string $fileType, string $accessToken): ?array;

    public function deleteFromBucket(string $bucketPath, string $accessToken): ?array;
}