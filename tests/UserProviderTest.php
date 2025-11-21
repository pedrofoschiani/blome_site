<?php

namespace Blome\Tests; // Corrigido para Tests, não Blome\Tests

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Blome\Services\UserProvider;
use Blome\Services\SupabaseClientInterface;

class UserProviderTest extends TestCase
{
    private MockObject $mockSupabaseClient;
    private UserProvider $userProvider;

    /**
     * Função 'setUp' (executada antes de cada teste)
     */
    protected function setUp(): void
    {
        // Cria o mock UMA VEZ para todos os testes
        $this->mockSupabaseClient = $this->createMock(SupabaseClientInterface::class);
        
        // Cria o provider UMA VEZ para todos os testes
        $this->userProvider = new UserProvider($this->mockSupabaseClient);
    }

    /**
     * Teste 1: Admin
     */
    public function testFetchesAdminProfileCorrectly()
    {
        // 1. Arrange (Cenário)
        $userId = 'user-123';
        $userRole = 'admin';
        $token = 'fake-token-456';
        $endpoint = 'admins_info?select=full_name,avatar_url&id=eq.' . $userId;
        $mockProfile = ['full_name' => 'Admin Teste', 'avatar_url' => 'avatar.png'];
        
        // Configure o mock (que foi criado no setUp)
        $this->mockSupabaseClient
             ->method('fetch')
             ->with($endpoint, $token) // <-- Deve esperar os 2 argumentos
             ->willReturn([$mockProfile]); // Retorno é um array

        // 2. Act (Ação)
        // Use o provider (que foi criado no setUp)
        $result = $this->userProvider->getUserProfile($userId, $userRole, $token);

        // 3. Assert (Verificação)
        $this->assertNotNull($result);
        $this->assertEquals('Admin Teste', $result['full_name']);
        $this->assertEquals('avatar.png', $result['avatar_url']);
    }

    /**
     * Teste 2: Professor
     */
    public function testFetchesProfessorProfileCorrectly()
    {
        // 1. Arrange
        $userId = 'prof-uuid-456';
        $userRole = 'professor';
        $token = 'fake-prof-token';
        $endpoint = 'professors_info?select=full_name,avatar_url&id=eq.' . $userId;
        $mockProfile = ['full_name' => 'Professor Teste', 'avatar_url' => 'prof.png'];

        // Configure o mock (que foi criado no setUp)
        $this->mockSupabaseClient
            ->expects($this->once())
            ->method('fetch')
            ->with($endpoint, $token) // <-- Deve esperar os 2 argumentos
            ->willReturn([$mockProfile]); // Retorno é um array

        // 2. Act (Ação)
        // Use o provider (que foi criado no setUp)
        $profile = $this->userProvider->getUserProfile($userId, $userRole, $token);

        // 3. Assert (Verificação)
        $this->assertNotNull($profile);
        $this->assertEquals('Professor Teste', $profile['full_name']);
    }

    /**
     * Teste 3: Aluno
     */
    public function testFetchesStudentProfileCorrectly()
    {
        // 1. Arrange
        $userId = 'student-uuid-789';
        $userRole = 'student';
        $token = 'fake-student-token';
        $endpoint = 'students?select=full_name,avatar_url&id=eq.' . $userId;
        $mockProfile = ['full_name' => 'Aluno Teste', 'avatar_url' => 'aluno.png'];

        // Configure o mock (que foi criado no setUp)
        $this->mockSupabaseClient
            ->expects($this->once())
            ->method('fetch')
            ->with($endpoint, $token) // <-- Deve esperar os 2 argumentos
            ->willReturn([$mockProfile]); // Retorno é um array

        // 2. Act (Ação)
        // Use o provider (que foi criado no setUp)
        $profile = $this->userProvider->getUserProfile($userId, $userRole, $token);

        // 3. Assert (Verificação)
        $this->assertNotNull($profile);
        $this->assertEquals('Aluno Teste', $profile['full_name']);
    }

    /**
     * Teste 4: Função Inválida
     */
    public function testReturnsNullForInvalidRole()
    {
        // 1. Arrange
        // NUNCA deve chamar 'fetch' se o role for inválido
        $this->mockSupabaseClient
            ->expects($this->never()) 
            ->method('fetch');

        // 2. Act
        // Use o provider (que foi criado no setUp)
        $profile = $this->userProvider->getUserProfile(
            'some-id',         // userId
            'INVALID_ROLE',    // userRole
            'fake-token'       // token
        );

        // 3. Assert
        $this->assertNull($profile);
    }
}