<?php

namespace Giovani\DocumentationEngine\Application\Services;

class NavigationBuilder
{
    public function build(array $slugs, string $current): array
    {
        sort($slugs);

        $index = array_search($current, $slugs);

        return [
            'prev' => $slugs[$index - 1] ?? null,
            'next' => $slugs[$index + 1] ?? null,
        ];
    }
}
