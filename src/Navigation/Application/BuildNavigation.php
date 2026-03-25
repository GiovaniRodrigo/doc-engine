<?php

namespace Giovani\DocumentationPlatformEngine\Navigation\Application;

use Giovani\DocumentationPlatformEngine\Navigation\Domain\TreeNode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BuildNavigation
{
    public function build(string $project): TreeNode
    {
        return Cache::remember(
            "doc-nav-{$project}",
            now()->addMinutes(10),
            fn () => $this->scan(
                storage_path("docs/{$project}"),
                ''
            )
        );
    }

    private function scan(string $dir, string $basePath): TreeNode
    {
        $nodes = [];

        foreach (scandir($dir) as $file) {

            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $full = "{$dir}/{$file}";
            $slug = trim("{$basePath}/{$file}", '/');

            if (is_dir($full)) {

                $nodes[] = $this->scan($full, $slug);

            } else {

                $nodes[] = new TreeNode(
                    title: Str::headline(str_replace('.md', '', $file)),
                    path: str_replace('.md', '', $slug),
                    isDirectory: false
                );
            }
        }

        return new TreeNode(
            title: basename($dir),
            path: trim($basePath, '/'),
            isDirectory: true,
            children: $nodes
        );
    }
}