<?php

namespace Giovani\DocumentationPlatformEngine\Navigation\Application;

class BuildBreadcrumbs
{
    public function build(string $path): array
    {
        $segments = explode('/', trim($path, '/'));

        $breadcrumbs = [];

        foreach ($segments as $index => $segment) {

            $breadcrumbs[] = [
                'title' => ucfirst(str_replace('-', ' ', $segment)),
                'path' => implode('/', array_slice($segments, 0, $index + 1))
            ];
        }

        return $breadcrumbs;
    }
}