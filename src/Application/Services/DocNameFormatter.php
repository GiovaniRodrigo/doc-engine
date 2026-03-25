<?php

namespace Giovani\DocumentationEngine\Application\Services;

class DocNameFormatter
{
    public function format(string $slugPart): string
    {
        $name = str_replace(['_', '-'], ' ', $slugPart);

        $name = preg_replace('/\badr\b/i', 'ADR', $name);

        $name = preg_replace('/\bprd\b/i', 'PRD', $name);

        $name = preg_replace('/\btrd\b/i', 'TRD', $name);

        $name = preg_replace('/\s+/', ' ', $name);

        return ucfirst(trim($name));
    }
}
