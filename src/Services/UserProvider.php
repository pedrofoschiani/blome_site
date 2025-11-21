<?php

namespace Blome\Services;

class UserProvider
{
    protected SupabaseClientInterface $supabaseClient;
    
    public function __construct(SupabaseClientInterface $supabaseClient)
    {
        $this->supabaseClient = $supabaseClient;
    }

    public function getUserProfile(string $userId, ?string $userRole, ?string $sessionToken): ?array
    {

        if ($userRole === null || $sessionToken === null) {
            return null;
        }

        $endpoint = null;

        switch ($userRole) {
            case 'admin':
                $endpoint = 'admins_info?select=full_name,avatar_url&id=eq.' . $userId;
                break;
            case 'professor':
                $endpoint = 'professors_info?select=full_name,avatar_url&id=eq.' . $userId;
                break;
            case 'student':
                $endpoint = 'students?select=full_name,avatar_url&id=eq.' . $userId;
                break;
            
            default:
                return null;
        }
        try {
            $result = $this->supabaseClient->fetch($endpoint, $sessionToken);
            
            return $result[0] ?? null;

        } catch (\Exception $e) {

            return null;
        }
    }
}