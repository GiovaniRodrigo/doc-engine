<?php

namespace Giovani\DocumentationEngine\Infrastructure\AI;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiDocumentationProvider implements AiProvider
{
    public function validateConfiguration(): void
    {
        $apiKey = (string) config('documentation-engine.ai.openai.api_key');

        if ($apiKey === '') {
            throw new RuntimeException('O provedor OpenAI requer uma chave de API válida. Configure a variável OPENAI_API_KEY no seu arquivo .env.');
        }

        $model = (string) config('documentation-engine.ai.openai.model');
        if ($model === '') {
            throw new RuntimeException('O modelo da OpenAI não foi configurado. Verifique a variável OPENAI_MODEL.');
        }
    }

    /**
     * @param  array<string, mixed>  $options
     */
    public function generate(string $prompt, array $options = []): string
    {
        $this->validateConfiguration();

        $apiKey = (string) config('documentation-engine.ai.openai.api_key');
        $model = trim((string) ($options['model'] ?? config('documentation-engine.ai.openai.model', 'gpt-5-mini')));

        $response = Http::baseUrl('https://api.openai.com/v1')
            ->timeout((int) config('documentation-engine.ai.openai.timeout', 30))
            ->retry((int) config('documentation-engine.ai.openai.retry_times', 1), 250)
            ->withToken($apiKey)
            ->acceptJson()
            ->post('/responses', [
                'model' => $model !== '' ? $model : (string) config('documentation-engine.ai.openai.model', 'gpt-5-mini'),
                'input' => $prompt,
                'text' => [
                    'format' => [
                        'type' => 'text',
                    ],
                ],
                'max_output_tokens' => (int) config('documentation-engine.ai.openai.max_output_tokens', 4000),
            ]);

        if ($response->failed()) {
            $message = Arr::get($response->json(), 'error.message')
                ?? 'Nao foi possivel gerar conteudo com a OpenAI.';

            throw new RuntimeException($message);
        }

        $content = $this->extractContent($response->json());

        if ($content === '') {
            throw new RuntimeException('A OpenAI nao retornou texto para este documento.');
        }

        return trim($content);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function extractContent(array $payload): string
    {
        $outputText = Arr::get($payload, 'output_text');

        if (is_string($outputText) && trim($outputText) !== '') {
            return $outputText;
        }

        $fragments = [];

        foreach ((array) Arr::get($payload, 'output', []) as $outputItem) {
            if (! is_array($outputItem)) {
                continue;
            }

            foreach ((array) Arr::get($outputItem, 'content', []) as $contentItem) {
                if (! is_array($contentItem)) {
                    continue;
                }

                $text = Arr::get($contentItem, 'text')
                    ?? Arr::get($contentItem, 'output_text');

                if (is_string($text) && trim($text) !== '') {
                    $fragments[] = $text;
                }
            }
        }

        return trim(implode("\n", $fragments));
    }
}
