<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Route;

class WebhookTest extends TestCase
{
    /** @test */
    public function it_can_receive_github_webhook_notifications()
    {
        $payload = [
            'ref' => 'refs/heads/main',
            'commits' => [
                [
                    'id' => 'a1b2c3d4',
                    'message' => 'Update documentation',
                ]
            ]
        ];

        $response = $this->postJson('/docs/webhooks/github', $payload);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Documentation synced.']);
    }

    /** @test */
    public function it_validates_webhook_signatures()
    {
        config()->set('documentation-engine.webhook_secret', 'secret');

        $payload = ['foo' => 'bar'];
        $signature = 'sha256=' . hash_hmac('sha256', json_encode($payload), 'wrong-secret');

        $response = $this->postJson('/docs/webhooks/github', $payload, [
            'X-Hub-Signature-256' => $signature
        ]);

        $this->assertEquals(403, $response->getStatusCode());
        
        $correctSignature = 'sha256=' . hash_hmac('sha256', json_encode($payload), 'secret');
        $response = $this->postJson('/docs/webhooks/github', $payload, [
            'X-Hub-Signature-256' => $correctSignature
        ]);
        
        $response->assertStatus(200);
    }
}
