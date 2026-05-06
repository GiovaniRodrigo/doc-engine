<?php

namespace Giovani\DocumentationEngine\Application\Services;

class BreadcrumbBuilder
{
    public function build(string $slug): array
    {
        $parts = explode('.', $slug);

        $breadcrumbs = [];

        $current = '';

        foreach ($parts as $index => $part) {

            $current .= ($index ? '.' : '').$part;

            $breadcrumbs[] = [
                'title' => ucfirst(str_replace('-', ' ', $part)),
                'slug' => $current,
            ];
        }

        return $breadcrumbs;
    }
}
