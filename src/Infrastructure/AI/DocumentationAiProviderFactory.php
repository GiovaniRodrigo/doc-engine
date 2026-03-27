<?php

namespace Giovani\DocumentationEngine\Infrastructure\AI;

use RuntimeException;

class DocumentationAiProviderFactory
{
    public function make(?string $provider = null): AiProvider
    {
        $provider = strtolower(trim((string) ($provider ?: config('documentation-engine.ai.driver', 'openai'))));

        return match ($provider) {
            'openai' => app(OpenAiDocumentationProvider::class),
            'gemini' => app(GeminiDocumentationProvider::class),
            default => throw new RuntimeException("Provedor de IA nao suportado para documentacao: {$provider}."),
        };
    }
}
