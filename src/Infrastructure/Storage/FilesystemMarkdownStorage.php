<?php

namespace Giovani\DocumentationEngine\Infrastructure\Storage;

use Illuminate\Support\Facades\File;

class FilesystemMarkdownStorage
{
    public function __construct(
        protected string $basePath
    ) {}

    public function all(): array
    {
        if (!is_dir($this->basePath)) {
            return [];
        }

        return collect(File::allFiles($this->basePath))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'md')
            ->map(function ($file) {

                $absolute = $file->getRealPath();

                $relative = str_replace(
                    $this->basePath . DIRECTORY_SEPARATOR,
                    '',
                    $absolute
                );

                return [
                    'path' => $absolute,
                    'relative' => $relative,
                    'slug' => $this->slugFromRelative($relative),
                    'content' => File::get($absolute),
                ];
            })
            ->values()
            ->toArray();
    }

    protected function slugFromRelative(string $relative): string
    {
        return str_replace(
            ['.md', '/', '\\'],
            ['', '.', '.'],
            strtolower($relative)
        );
    }
}