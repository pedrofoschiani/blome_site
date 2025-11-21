<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use Blome\Services\ProfileUpdateHandler;
use Blome\Services\ProfileUpdater; // A classe que vamos mockar

class ProfileUpdateHandlerTest extends TestCase
{
    private $updaterMock;
    private $mockSession;
    private $mockServer;

    protected function setUp(): void
    {
        // 1. Criamos o "dublê" (Mock) do ProfileUpdater
        $this->updaterMock = $this->createMock(ProfileUpdater::class);
        
        // 2. Criamos uma sessão de Admin falsa
        $this->mockSession = [
            'user_id' => 'admin-test-id',
            'user_role' => 'admin',
            'access_token' => 'admin-test-token'
        ];

        // 3. Criamos um $_SERVER falso
        $this->mockServer = [
            'HTTP_REFERER' => 'http://localhost/pagina-de-perfil'
        ];
    }

    /**
     * Teste 14: Testa a atualização APENAS do nome
     */
    public function testUpdatesOnlyName()
    {
        // 1. Arrange (Cenário)
        $mockPost = ['full_name' => 'Novo Nome'];
        $mockFiles = [];
        
        // Esperamos que o método updateProfile() seja chamado UMA VEZ
        $this->updaterMock
             ->expects($this->once())
             ->method('updateProfile')
             ->with('admin-test-id', 'admin', 'admin-test-token', ['full_name' => 'Novo Nome']);

        // 2. Act (Ação)
        $handler = new ProfileUpdateHandler($this->updaterMock, $this->mockSession, $mockPost, $mockFiles, $this->mockServer);
        $redirectUrl = $handler->handleRequest();

        // 3. Assert (Verificação)
        // Deve redirecionar para a página anterior com sucesso
        $this->assertEquals('http://localhost/pagina-de-perfil?success=true', $redirectUrl);
    }

    /**
     * Teste 15: Testa a atualização da SENHA (e força re-login)
     */
    public function testUpdatesPasswordAndForcesRelogin()
    {
        // 1. Arrange (Cenário)
        $mockPost = ['new_password' => 'NovaSenha123'];
        $mockFiles = [];

        // Esperamos que o método updateUserAuth() seja chamado UMA VEZ
        $this->updaterMock
             ->expects($this->once())
             ->method('updateUserAuth')
             ->with('admin-test-token', ['password' => 'NovaSenha123']);
        
        // 2. Act (Ação)
        $handler = new ProfileUpdateHandler($this->updaterMock, $this->mockSession, $mockPost, $mockFiles, $this->mockServer);
        $redirectUrl = $handler->handleRequest();

        // 3. Assert (Verificação)
        // Deve redirecionar para a página de login
        $this->assertEquals('../../pages/login/login.page.php?success=auth_updated', $redirectUrl);
    }
    
    /**
     * Teste 16: Testa a atualização do AVATAR
     */
    public function testUpdatesAvatar()
    {
        // 1. Arrange (Cenário)
        $mockPost = [];
        $mockFile = [
            'name' => 'teste.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/file',
            'error' => 0,
            'size' => 123
        ];
        $mockFiles = ['avatar_file' => $mockFile]; // Como o PHP organiza

        // Esperamos que o método updateAvatar() seja chamado UMA VEZ
        $this->updaterMock
             ->expects($this->once())
             ->method('updateAvatar')
             ->with('admin-test-id', 'admin', 'admin-test-token', $mockFile, 'users-icons');

        // 2. Act (Ação)
        $handler = new ProfileUpdateHandler($this->updaterMock, $this->mockSession, $mockPost, $mockFiles, $this->mockServer);
        $redirectUrl = $handler->handleRequest();

        // 3. Assert (Verificação)
        // Deve redirecionar para a página anterior com sucesso
        $this->assertEquals('http://localhost/pagina-de-perfil?success=true', $redirectUrl);
    }
}