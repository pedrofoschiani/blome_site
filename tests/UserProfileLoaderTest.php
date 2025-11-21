<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use Blome\Services\UserProfileLoader; // A classe que estamos testando
use Blome\Services\UserProvider; // A dependência que vamos mockar

class UserProfileLoaderTest extends TestCase
{
    // Mock do UserProvider
    private $userProviderMock;

    /**
     * Isso roda antes de CADA teste
     */
    protected function setUp(): void
    {
        // Criamos o mock que simula o UserProvider
        $this->userProviderMock = $this->createMock(UserProvider::class);
    }

    /**
     * Teste 5: Carrega Administradores
     */
    public function testAdminGetsProfileData()
    {
        // 1. Arrange (Cenário)
        $adminSession = [
            'user_role' => 'admin',
            'user_id' => 'admin-id-123',
            'access_token' => 'admin-token-abc'
        ];
        
        $adminProfile = [
            'full_name' => 'Admin de Teste',
            'avatar_url' => 'admin.png'
        ];

        // Configura o mock para retornar o perfil esperado
        $this->userProviderMock
             ->method('getUserProfile')
             ->with('admin-id-123', 'admin', 'admin-token-abc')
             ->willReturn($adminProfile); 
        
        // 2. Act (Ação)
        $loader = new UserProfileLoader($this->userProviderMock, $adminSession);
        
        // Chamamos o método que estamos testando
        $data = $loader->getProfileData();

        // 3. Assert (Verificação)
        $this->assertEquals('Admin de Teste', $data['full_name']);
        $this->assertEquals('admin.png', $data['avatar_url']);
    }

    /**
     * Teste 6: Carrega Professores
     */
    public function testProfessorGetsProfileData()
    {
        // 1. Arrange (Cenário)
        $professorSession = [
            'user_role' => 'professor',
            'user_id' => 'prof-id-456',
            'access_token' => 'prof-token-def'
        ];
        
        $professorProfile = [
            'full_name' => 'Professor de Teste',
            'avatar_url' => 'professor.png'
        ];

        // Configura o mock para retornar o perfil esperado
        $this->userProviderMock
             ->method('getUserProfile')
             ->with('prof-id-456', 'professor', 'prof-token-def')
             ->willReturn($professorProfile); 
        
        // 2. Act (Ação)
        $loader = new UserProfileLoader($this->userProviderMock, $professorSession);
        
        // Chamamos o método que estamos testando
        $data = $loader->getProfileData();

        // 3. Assert (Verificação)
        $this->assertEquals('Professor de Teste', $data['full_name']);
        $this->assertEquals('professor.png', $data['avatar_url']);
    }

    /**
     * Teste 7: Carrega Alunos
     */
    public function testStudentGetsProfileData()
    {
        // 1. Arrange (Cenário)
        $studentSession = [
            'user_role' => 'student',
            'user_id' => 'student-id-789',
            'access_token' => 'student-token-ghi'
        ];

        $studentProfile = [
            'full_name' => 'Aluno de Teste',
            'avatar_url' => 'aluno.png'
        ];

        // Configura o mock para retornar o perfil esperado
        $this->userProviderMock
             ->method('getUserProfile')
             ->with('student-id-789', 'student', 'student-token-ghi')
             ->willReturn($studentProfile);
        
        // 2. Act (Ação)
        $loader = new UserProfileLoader($this->userProviderMock, $studentSession);

        // Chamamos o método que estamos testando
        $data = $loader->getProfileData();

        // 3. Assert (Verificação)
        $this->assertEquals('Aluno de Teste', $data['full_name']);
        $this->assertEquals('aluno.png', $data['avatar_url']);
    }
}