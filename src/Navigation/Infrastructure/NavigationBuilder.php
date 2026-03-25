<?php

namespace Giovani\DocumentationPlatformEngine\Navigation\Infrastructure;

use Illuminate\Support\Facades\File;
use Giovani\DocumentationPlatformEngine\Navigation\Domain\NavigationNode;

class NavigationBuilder
{
    public function build(string $basePath): array
    {
        return $this->scan($basePath);
    }

    private function scan(string $path): array
    {
        $nodes = [];

        foreach (File::directories($path) as $dir) {

            $nodes[] = new NavigationNode(
                name: basename($dir),
                path: $dir,
                children: $this->scan($dir)
            );
        }

        foreach (File::files($path) as $file) {

            if ($file->getExtension() !== 'md') {
                continue;
            }

            $nodes[] = new NavigationNode(
                name: $file->getFilenameWithoutExtension(),
                path: $file->getRealPath()
            );
        }

        return $nodes;
    }
}