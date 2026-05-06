<?php

namespace Giovani\DocumentationEngine\Infrastructure\AI;

interface AiProvider
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function generate(string $prompt, array $options = []): string;

    /**
     * Proactively validate that the provider has everything it needs to work.
     * 
     * @throws \RuntimeException If configuration is missing or invalid.
     */
    public function validateConfiguration(): void;
}
