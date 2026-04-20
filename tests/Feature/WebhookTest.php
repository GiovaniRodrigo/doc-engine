<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class WebhookTest extends TestCase
{
    /** @test */
    public function it_can_receive_github_webhook_notifications()
    {
        // RF03 - Suporte a Webhooks
        // Esta rota ainda não existe, então o teste deve falhar (404)
        $payload = [
            'ref' => 'refs/heads/main',
            'commits' => [
                [
                    'id' => 'a1b2c3d4',
                    'message' => 'Update documentation',
                ]
            ]
        ];

        $response = $this->postJson('/docs/webhooks/github', $payload, [
            'X-GitHub-Event' => 'push',
            'X-Hub-Signature-256' => 'sha256=invalid-signature-for-now'
        ]);

        // Esperamos que eventualmente isso funcione, mas no momento deve falhar
        $this->assertNotEquals(404, $response->getStatusCode(), 'Webhook route /docs/webhooks/github is missing.');
    }

    /** @test */
    public function it_validates_webhook_signatures()
    {
        // RF04 - Validação de Segurança
        config()->set('documentation-engine.webhook_secret', 'secret');

        $response = $this->postJson('/docs/webhooks/github', [], [
            'X-Hub-Signature-256' => 'sha256=wrong-signature'
        ]);

        $this->assertEquals(403, $response->getStatusCode(), 'Webhook should reject invalid signatures.');
    }
}
