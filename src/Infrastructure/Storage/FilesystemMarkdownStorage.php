<?php

namespace Giovani\DocumentationEngine\Infrastructure\Storage;

class FilesystemMarkdownStorage
{
    public function __construct(private string $basePath) {}

    public function all(): array
    {
        return glob($this->basePath . '/*.md');
    }

    public function get(string $file): string
    {
        return file_get_contents($file);
    }

    public function put(string $file, string $content): void
    {
        file_put_contents($file, $content);
    }
}
