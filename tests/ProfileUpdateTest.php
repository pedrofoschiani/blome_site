<?php
namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use Blome\Services\ProfileUpdater;
use Blome\Services\SupabaseClientInterface;

class ProfileUpdaterTest extends TestCase
{
    private $supabaseClientMock;

    protected function setUp(): void
    {
        $this->supabaseClientMock = $this->createMock(SupabaseClientInterface::class);
    }

    /**
     * Teste 8: Admins podem atualizar seu nome
     */
    public function testAdminCanUpdateFullName()
    {
        // 1. Arrange (Cenário)
        $userId = 'admin-id-123';
        $role = 'admin';
        $token = 'admin-token-abc';
        
        // Os dados que vêm do formulário
        $dataToUpdate = ['full_name' => 'Novo Nome do Admin'];

        // O endpoint do Supabase que esperamos que seja chamado
        $expectedEndpoint = 'admins_info?id=eq.' . $userId;

        // Configuramos o mock:
        // Esperamos que o método "patch" seja chamado UMA VEZ
        $this->supabaseClientMock
             ->expects($this->once())
             ->method('patch')
             ->with($expectedEndpoint, $dataToUpdate, $token)
             ->willReturn([['full_name' => 'Novo Nome do Admin']]);

        // 2. Act (Ação)
        $updater = new ProfileUpdater($this->supabaseClientMock);
        $result = $updater->updateProfile($userId, $role, $token, $dataToUpdate);

        // 3. Assert (Verificação)
        $this->assertNotNull($result);
        $this->assertEquals('Novo Nome do Admin', $result[0]['full_name']);
    }

    /**
     * Teste 9: Professor podem atualizar seu nome
     */

    public function testProfessorCanUpdateFullName()
    {
        // 1. Arrange (Cenário)
        $userId = 'professor-id-456';
        $role = 'professor';
        $token = 'professor-token-def';

        // Os dados que vêm do formulário
        $dataToUpdate = ['full_name' => 'Novo Nome do Professor'];

        // O endpoint do Supabase que esperamos que seja chamado
        $expectedEndpoint = 'professors_info?id=eq.' . $userId;

        // Configuramos o mock:
        // Esperamos que o método "patch" seja chamado UMA VEZ
        $this->supabaseClientMock
             ->expects($this->once())
             ->method('patch')
             ->with($expectedEndpoint, $dataToUpdate, $token)
             ->willReturn([['full_name' => 'Novo Nome do Professor']]);
        
        // 2. Act (Ação)
        $updater = new ProfileUpdater($this->supabaseClientMock);
        $result = $updater->updateProfile($userId, $role, $token, $dataToUpdate);

        // 3. Assert (Verificação)
        $this->assertNotNull($result);
        $this->assertEquals('Novo Nome do Professor', $result[0]['full_name']);
    }

    /**
     * Teste 10: Alunos podem atualizar seu nome
     */
    public function testStudentCanUpdateFullName()
    {
        // 1. Arrange (Cenário)
        $userId = 'student-id-789';
        $role = 'student';
        $token = 'student-token-ghi';

        // Os dados que vêm do formulário
        $dataToUpdate = ['full_name' => 'Novo Nome do Aluno'];

        // O endpoint do Supabase que esperamos que seja chamado
        $expectedEndpoint = 'students?id=eq.' . $userId;

        // Configuramos o mock:
        // Esperamos que o método "patch" seja chamado UMA VEZ
        $this->supabaseClientMock
             ->expects($this->once())
             ->method('patch')
             ->with($expectedEndpoint, $dataToUpdate, $token)
             ->willReturn([['full_name' => 'Novo Nome do Aluno']]);

        // 2. Act (Ação)
        $updater = new ProfileUpdater($this->supabaseClientMock);
        $result = $updater->updateProfile($userId, $role, $token, $dataToUpdate);

        // 3. Assert (Verificação)
        $this->assertNotNull($result);
        $this->assertEquals('Novo Nome do Aluno', $result[0]['full_name']);
    }

    /**
     * Teste 11: Usuários podem atualizar o email
     */
    public function testUserCanUpdateEmail()
    {
        // 1. Arrange (Cenário)
        $token = 'user-token-abc';
        $dataToUpdate = ['email' => 'novo.email@teste.com'];

        // Configuramos o mock:
        $mockSuccessResponse = [
            'id' => 'user-id-123',
            'email' => 'novo.email@teste.com',
            'new_email' => 'novo.email@teste.com'
        ];

        // Esperamos que o método "updateAuth" seja chamado
        $this->supabaseClientMock
             ->expects($this->once())
             ->method('updateAuth')
             ->with($dataToUpdate, $token)
             ->willReturn($mockSuccessResponse);

        // 2. Act (Ação)
        $updater = new ProfileUpdater($this->supabaseClientMock);
        $result = $updater->updateUserAuth($token, $dataToUpdate);

        // 3. Assert (Verificação)
        $this->assertNotNull($result);
        $this->assertEquals('novo.email@teste.com', $result['email']);
    }

    /**
     * Teste 12: Usuários podem atualizar a senha
     */
    public function testUserCanUpdatePassword()
    {
        // 1. Arrange (Cenário)
        $token = 'user-token-abc';
        $dataToUpdate = ['password' => 'NovaSenhaSegura123'];

        // O Supabase Auth retorna o objeto do usuário (sem a senha)
        $mockSuccessResponse = [
            'id' => 'user-id-123',
            'aud' => 'authenticated',
            'email' => 'teste@teste.com'
        ];

        // Esperamos que o método "updateAuth" do cliente seja chamado
        $this->supabaseClientMock
             ->expects($this->once())
             ->method('updateAuth')
             ->with($dataToUpdate, $token) // Com os dados (senha) e o token
             ->willReturn($mockSuccessResponse);

        // 2. Act (Ação)
        $updater = new ProfileUpdater($this->supabaseClientMock);
        $result = $updater->updateUserAuth($token, $dataToUpdate);

        // 3. Assert (Verificação)
        $this->assertNotNull($result);
        $this->assertEquals('user-id-123', $result['id']);
    }

    /**
     * Teste 13: Usuário pode atualizar o avatar
     */
    public function testUserCanUpdateAvatar()
    {
        // Arrange
        $userId = 'admin-id-123';
        $role = 'admin';
        $token = 'admin-token-abc';

        $fileData = [
            'name' => 'meu-avatar.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/php_temp_file_123',
            'error' => 0,
            'size' => 12345
        ];

        $bucketName = 'users-icon';
        $folder = 'admin';

        $expectedBucketPath = "{$bucketName}/{$folder}/{$userId}-";

        // URL pública final esperada
        $publicUrl =
            "https://ffcrtnubzhtyqnzfyfee.supabase.co/storage/v1/object/public/" .
            $expectedBucketPath;

        // Mock do upload
        $this->supabaseClientMock
            ->expects($this->once())
            ->method('uploadToBucket')
            ->with(
                $expectedBucketPath,            
                $fileData['tmp_name'],
                $fileData['type'],
                $token
            )
            ->willReturn(['Key' => "{$folder}/{$userId}-"]);

        // Mock do PATCH via updateProfile()
        $dbEndpoint = "admins_info?id=eq.{$userId}";
        $dbData = ['avatar_url' => $publicUrl];

        $this->supabaseClientMock
            ->expects($this->once())
            ->method('patch')
            ->with($dbEndpoint, $dbData, $token)
            ->willReturn([['avatar_url' => $publicUrl]]);

        // Act
        $updater = new ProfileUpdater($this->supabaseClientMock);
        $result = $updater->updateAvatar($userId, $role, $token, $fileData, $bucketName);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($publicUrl, $result[0]['avatar_url']);
    }

}