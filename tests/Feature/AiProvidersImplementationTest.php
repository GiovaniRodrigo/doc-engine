<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Giovani\DocumentationEngine\Infrastructure\AI\OpenAiDocumentationProvider;
use Giovani\DocumentationEngine\Infrastructure\AI\GeminiDocumentationProvider;

class AiProvidersImplementationTest extends TestCase
{
    #[Test]
    public function openai_provider_makes_correct_http_request()
    {
        config()->set('documentation-engine.ai.openai.api_key', 'sk-test-key');
        config()->set('documentation-engine.ai.openai.model', 'gpt-5-mini');

        Http::fake([
            'api.openai.com/*' => Http::response([
                'output_text' => 'Resposta da OpenAI'
            ], 200)
        ]);

        $provider = new OpenAiDocumentationProvider();
        $result = $provider->generate('Meu prompt');

        $this->assertSame('Resposta da OpenAI', $result);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.openai.com/v1/responses' &&
                   $request->hasHeader('Authorization', 'Bearer sk-test-key') &&
                   $request['model'] === 'gpt-5-mini' &&
                   $request['input'] === 'Meu prompt';
        });
    }

    #[Test]
    public function gemini_provider_makes_correct_http_request()
    {
        config()->set('documentation-engine.ai.gemini.api_key', 'gemini-test-key');
        config()->set('documentation-engine.ai.gemini.model', 'gemini-2.5-flash');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Resposta do Gemini']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $provider = new GeminiDocumentationProvider();
        $result = $provider->generate('Meu prompt');

        $this->assertSame('Resposta do Gemini', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent') &&
                   $request->hasHeader('x-goog-api-key', 'gemini-test-key') &&
                   $request['contents'][0]['parts'][0]['text'] === 'Meu prompt';
        });
    }
}
