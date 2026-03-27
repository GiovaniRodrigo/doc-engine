<?php

namespace Giovani\DocumentationEngine\Infrastructure\AI;

interface AiProvider
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function generate(string $prompt, array $options = []): string;
}
