<?php

namespace Giovani\DocumentationEngine\Infrastructure\AI;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiDocumentationProvider implements AiProvider
{
    public function validateConfiguration(): void
    {
        $apiKey = (string) config('documentation-engine.ai.gemini.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('O provedor Gemini requer uma chave de API válida. Configure a variável GEMINI_API_KEY no seu arquivo .env.');
        }

        $model = (string) config('documentation-engine.ai.gemini.model');
        if ($model === '') {
            throw new RuntimeException('O modelo do Gemini não foi configurado. Verifique a variável GEMINI_MODEL.');
        }
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function generate(string $prompt, array $options = []): string
    {
        $this->validateConfiguration();

        $apiKey = (string) config('documentation-engine.ai.gemini.api_key');
        $model = trim((string) ($options['model'] ?? config('documentation-engine.ai.gemini.model', 'gemini-2.5-flash')));
        $model = $model !== '' ? $model : (string) config('documentation-engine.ai.gemini.model', 'gemini-2.5-flash');

        $response = Http::baseUrl('https://generativelanguage.googleapis.com/v1beta')
            ->timeout((int) config('documentation-engine.ai.gemini.timeout', 30))
            ->retry((int) config('documentation-engine.ai.gemini.retry_times', 1), 250)
            ->acceptJson()
            ->withHeaders([
                'x-goog-api-key' => $apiKey,
            ])
            ->post("/models/{$model}:generateContent", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'maxOutputTokens' => (int) config('documentation-engine.ai.gemini.max_output_tokens', 4000),
                ],
            ]);

        if ($response->failed()) {
            $message = Arr::get($response->json(), 'error.message')
                ?? 'Nao foi possivel gerar conteudo com o Gemini.';

            throw new RuntimeException($message);
        }

        $content = $this->extractContent($response->json());

        if ($content === '') {
            throw new RuntimeException('O Gemini nao retornou texto para este documento.');
        }

        return trim($content);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function extractContent(array $payload): string
    {
        $fragments = [];

        foreach ((array) Arr::get($payload, 'candidates', []) as $candidate) {
            if (! is_array($candidate)) {
                continue;
            }

            foreach ((array) Arr::get($candidate, 'content.parts', []) as $part) {
                if (! is_array($part)) {
                    continue;
                }

                $text = Arr::get($part, 'text');

                if (is_string($text) && trim($text) !== '') {
                    $fragments[] = $text;
                }
            }
        }

        return trim(implode("\n", $fragments));
    }
}
