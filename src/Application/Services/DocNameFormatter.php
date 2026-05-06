<?php

namespace Giovani\DocumentationEngine\Application\Services;

class DocNameFormatter
{
    public function format(string $slugPart): string
    {
        if (preg_match('/^adr-(\d+)-([a-z0-9]+)/i', $slugPart, $matches) === 1) {
            return sprintf('ADR %s %s', $matches[1], ucfirst(strtolower($matches[2])));
        }

        $name = str_replace(['_', '-'], ' ', $slugPart);

        $name = preg_replace('/\badr\b/i', 'ADR', $name);

        $name = preg_replace('/\bprd\b/i', 'PRD', $name);

        $name = preg_replace('/\btrd\b/i', 'TRD', $name);

        $name = preg_replace('/\s+/', ' ', $name);

        return ucfirst(trim($name));
    }
}
