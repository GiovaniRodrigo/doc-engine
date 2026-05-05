<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Giovani\DocumentationEngine\Tests\TestCase;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;
use Mockery;

class AiFeaturesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('documentation-engine.ai.enabled', true);
        $this->createDocument('guia', '# Guia');
    }

    #[Test]
    public function it_can_generate_a_tldr_summary()
    {
        $this->mock(AiProvider::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->with(Mockery::pattern('/resumo curto \(TL;DR\)/'), Mockery::any())
                ->andReturn('Este é um resumo.');
        });

        $response = $this->postJson('/docs/guia/generate', [
            'type' => 'tldr'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['content' => 'Este é um resumo.']);
    }

    #[Test]
    public function it_can_suggest_smart_tags()
    {
        $this->mock(AiProvider::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->with(Mockery::pattern('/Sugira ate 5 tags/'), Mockery::any())
                ->andReturn('laravel, docs, php');
        });

        $response = $this->postJson('/docs/guia/generate', [
            'type' => 'suggest_tags'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['content' => 'laravel, docs, php']);
    }

    #[Test]
    public function it_provides_a_chat_interface_context()
    {
        $this->mock(AiProvider::class, function ($mock) {
            $mock->shouldReceive('generate')
                ->with(Mockery::pattern('/assistente de documentacao/'), Mockery::any())
                ->andReturn('A resposta para sua dúvida.');
        });

        $response = $this->postJson('/docs/guia/chat', [
            'message' => 'Como instalo o pacote?'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['response' => 'A resposta para sua dúvida.']);
    }
}
