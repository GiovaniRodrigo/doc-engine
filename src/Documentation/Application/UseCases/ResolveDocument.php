<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Application\UseCases;

use Illuminate\Support\Str;

class ResolveDocument
{
    public function resolve(string $project, ?string $path): array
    {
        $base = storage_path("docs/{$project}");

        $path = trim($path ?? '', '/');

        $file = $path
            ? "{$base}/{$path}.md"
            : "{$base}/README.md";

        if (file_exists($file)) {
            return [
                'type' => 'file',
                'html' => Str::markdown(file_get_contents($file))
            ];
        }

        $dir = $path ? "{$base}/{$path}" : $base;

        if (is_dir($dir)) {

            return [
                'type' => 'index',
                'items' => collect(scandir($dir))
                    ->reject(fn($f) => in_array($f, ['.', '..']))
                    ->map(fn($f) => str_replace('.md', '', $f))
                    ->values()
            ];
        }

        abort(404);
    }
}
