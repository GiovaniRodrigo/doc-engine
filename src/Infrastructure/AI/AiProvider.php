<?php

namespace Giovani\DocumentationEngine\Infrastructure\AI;

interface AiProvider
{
    public function generate(string $prompt): string;
}
