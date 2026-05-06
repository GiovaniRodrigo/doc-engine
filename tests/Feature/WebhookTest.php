<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\Test;

class WebhookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Process::fake([
            '*git*pull*' => Process::result('Already up to date.'),
            '*git*rev-parse*HEAD*' => Process::result('fake-hash'),
            '*git*diff-tree*' => Process::result(''),
        ]);
    }

    #[Test]
    public function it_can_receive_github_webhook_notifications()
    {
        $payload = [
            'ref' => 'refs/heads/main',
            'commits' => [
                [
                    'id' => 'a1b2c3d4',
                    'message' => 'Update documentation',
                ],
            ],
        ];

        $response = $this->postJson('/docs/webhooks/github', $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Documentation synced.');
        $response->assertJsonPath('summary.created_versions', 0);
    }

    #[Test]
    public function it_validates_webhook_signatures()
    {
        config()->set('documentation-engine.webhook_secret', 'secret');

        $payload = ['foo' => 'bar'];
        $signature = 'sha256='.hash_hmac('sha256', json_encode($payload), 'wrong-secret');

        $response = $this->postJson('/docs/webhooks/github', $payload, [
            'X-Hub-Signature-256' => $signature,
        ]);

        $this->assertEquals(403, $response->getStatusCode());

        $correctSignature = 'sha256='.hash_hmac('sha256', json_encode($payload), 'secret');
        $response = $this->postJson('/docs/webhooks/github', $payload, [
            'X-Hub-Signature-256' => $correctSignature,
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_requires_github_signature_only_when_secret_is_configured()
    {
        config()->set('documentation-engine.webhook_secret', null);

        $response = $this->postJson('/docs/webhooks/github', [
            'ref' => 'refs/heads/main',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_can_receive_gitlab_webhook_notifications()
    {
        config()->set('documentation-engine.webhook_secret', 'secret');

        $payload = [
            'object_kind' => 'push',
            'after' => 'a1b2c3d4',
        ];

        $response = $this->postJson('/docs/webhooks/gitlab', $payload, [
            'X-Gitlab-Token' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Documentation synced.');
        $response->assertJsonPath('summary.created_versions', 0);
    }

    #[Test]
    public function it_validates_gitlab_webhook_tokens()
    {
        config()->set('documentation-engine.webhook_secret', 'secret');

        $response = $this->postJson('/docs/webhooks/gitlab', [], [
            'X-Gitlab-Token' => 'wrong-token',
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_ignores_webhooks_for_non_target_branches()
    {
        config()->set('documentation-engine.webhook_branch', 'main');

        $response = $this->postJson('/docs/webhooks/github', [
            'ref' => 'refs/heads/feature/docs',
        ]);

        $response->assertStatus(202);
        $response->assertJson(['message' => 'Webhook ignored for this branch.']);
    }
}
