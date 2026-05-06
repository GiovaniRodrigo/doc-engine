<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Mockery;
use PHPUnit\Framework\Attributes\Test;

class AiLoggingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('documentation-engine.ai.enabled', true);
        $this->createDocument('guia', '# Guia');
    }

    #[Test]
    public function it_logs_successful_ai_generation()
    {
        $this->mock(AiProvider::class, function ($mock) {
            $mock->shouldReceive('validateConfiguration')->once();
            $mock->shouldReceive('generate')->once()->andReturn('Conteudo gerado');
        });

        Log::shouldReceive('info')
            ->once()
            ->with('AI documentation content generated.', Mockery::on(function ($context) {
                return $context['slug'] === 'guia' &&
                       $context['type'] === 'general' &&
                       isset($context['input_size']) &&
                       isset($context['output_size']);
            }));

        $this->postJson('/docs/guia/generate', [
            'prompt' => 'Melhore este texto',
        ]);
    }

    #[Test]
    public function it_logs_ai_generation_failures()
    {
        $this->mock(AiProvider::class, function ($mock) {
            $mock->shouldReceive('validateConfiguration')->once();
            $mock->shouldReceive('generate')->once()->andThrow(new \Exception('Erro na API'));
        });

        Log::shouldReceive('error')
            ->once()
            ->with('AI documentation generation failed.', Mockery::on(function ($context) {
                return $context['slug'] === 'guia' &&
                       $context['error'] === 'Erro na API';
            }));

        $this->postJson('/docs/guia/generate', [
            'prompt' => 'Melhore este texto',
        ]);
    }
}
