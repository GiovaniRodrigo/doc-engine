<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;

class GenerateWithAI
{
    public function __construct(private AiProvider $ai) {}

    /**
     * @param  array<string, mixed>  $options
     */
    public function execute(string $prompt, array $options = []): string
    {
        return $this->ai->generate($prompt, $options);
    }
}
