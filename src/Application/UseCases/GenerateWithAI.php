<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;

class GenerateWithAI
{
    public function __construct(private AiProvider $ai) {}

    public function execute(string $prompt): string
    {
        return $this->ai->generate($prompt);
    }
}
