<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class RealTimeCollaborationTest extends TestCase
{
    #[Test]
    public function collaboration_endpoint_registers_unauthenticated_session_presence(): void
    {
        $this->createDocument('guia-colab', '# Conteudo');

        // Primeiro batimento (cria sessao)
        $response = $this->postJson('/docs/guia-colab/collaboration');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'users' => [
                '*' => ['id', 'name', 'is_current', 'last_seen']
            ],
            'has_conflict'
        ]);

        $payload = $response->json();
        $this->assertCount(1, $payload['users']);
        $this->assertTrue($payload['users'][0]['is_current']);
        $this->assertFalse($payload['has_conflict']);
        $this->assertStringContainsString('Editor #', $payload['users'][0]['name']);
    }

    #[Test]
    public function collaboration_endpoint_registers_authenticated_user_presence(): void
    {
        $this->createDocument('guia-colab', '# Conteudo');

        $user = new User();
        $user->forceFill([
            'id' => 42,
            'name' => 'Maria Silva',
            'email' => 'maria@exemplo.com'
        ]);

        $response = $this->actingAs($user)->postJson('/docs/guia-colab/collaboration');

        $response->assertStatus(200);
        $payload = $response->json();
        $this->assertCount(1, $payload['users']);
        $this->assertEquals('Maria Silva', $payload['users'][0]['name']);
        $this->assertEquals('42', $payload['users'][0]['id']);
    }

    #[Test]
    public function collaboration_endpoint_returns_multiple_active_users_and_detects_conflict(): void
    {
        $this->createDocument('guia-colab', '# Conteudo');

        // Simula usuario anterior no cache
        $cacheKey = 'documentation-engine:collaboration:guia-colab';
        Cache::put($cacheKey, [
            'outra_sessao' => [
                'id' => 'outra_sessao',
                'name' => 'Joao Souza',
                'last_seen' => time() // ativo
            ]
        ], 60);

        // Envia batimento do usuario atual
        $response = $this->postJson('/docs/guia-colab/collaboration');

        $response->assertStatus(200);
        $payload = $response->json();

        // Deve conter Joao Souza e o usuario atual
        $this->assertCount(2, $payload['users']);
        $this->assertTrue($payload['has_conflict']);

        $names = collect($payload['users'])->pluck('name')->toArray();
        $this->assertContains('Joao Souza', $names);
    }

    #[Test]
    public function collaboration_endpoint_expires_inactive_users(): void
    {
        $this->createDocument('guia-colab', '# Conteudo');

        // Simula usuario expirado no cache (visto ha 20 segundos)
        $cacheKey = 'documentation-engine:collaboration:guia-colab';
        Cache::put($cacheKey, [
            'sessao_antiga' => [
                'id' => 'sessao_antiga',
                'name' => 'Usuario Ausente',
                'last_seen' => time() - 20
            ]
        ], 60);

        // Envia batimento do usuario atual
        $response = $this->postJson('/docs/guia-colab/collaboration');

        $response->assertStatus(200);
        $payload = $response->json();

        // O usuario antigo (20s) deve ter sido limpo, restando apenas o atual
        $this->assertCount(1, $payload['users']);
        $this->assertFalse($payload['has_conflict']);
        
        $diff = abs(time() - $payload['users'][0]['last_seen']);
        $this->assertLessThanOrEqual(2, $diff);
    }
}
