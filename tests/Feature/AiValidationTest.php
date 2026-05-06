<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AiValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('documentation-engine.ai.enabled', true);
        $this->createDocument('guia', '# Guia');
    }

    #[Test]
    public function it_returns_actionable_error_when_openai_key_is_missing()
    {
        config()->set('documentation-engine.ai.driver', 'openai');
        config()->set('documentation-engine.ai.openai.api_key', '');

        $response = $this->postJson('/docs/guia/generate', [
            'prompt' => 'Teste',
        ]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'O provedor OpenAI requer uma chave de API válida. Configure a variável OPENAI_API_KEY no seu arquivo .env.']);
    }

    #[Test]
    public function it_returns_actionable_error_when_gemini_key_is_missing()
    {
        config()->set('documentation-engine.ai.driver', 'gemini');
        config()->set('documentation-engine.ai.gemini.api_key', '');

        $response = $this->postJson('/docs/guia/generate', [
            'prompt' => 'Teste',
        ]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'O provedor Gemini requer uma chave de API válida. Configure a variável GEMINI_API_KEY no seu arquivo .env.']);
    }

    #[Test]
    public function it_allows_selecting_provider_via_request_and_validates_it()
    {
        config()->set('documentation-engine.ai.driver', 'openai');
        config()->set('documentation-engine.ai.openai.api_key', 'some-key');
        config()->set('documentation-engine.ai.gemini.api_key', '');

        $response = $this->postJson('/docs/guia/generate', [
            'prompt' => 'Teste',
            'provider' => 'gemini',
        ]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'O provedor Gemini requer uma chave de API válida. Configure a variável GEMINI_API_KEY no seu arquivo .env.']);
    }

    #[Test]
    public function it_enforces_input_size_limits()
    {
        $largeContent = str_repeat('a', 30001);

        $response = $this->postJson('/docs/guia/generate', [
            'content' => $largeContent,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }
}
