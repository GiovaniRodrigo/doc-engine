<?php

namespace Giovani\DocumentationEngine\Infrastructure\Storage;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

    public function getBySlug(string $slug): ?string
    {
        $path = $this->absolutePathFromSlug($slug);

        if (!File::exists($path)) {
            return null;
        }

        return File::get($path);
    }

    public function putBySlug(string $slug, string $content): string
    {
        $path = $this->absolutePathFromSlug($slug);

        File::ensureDirectoryExists(dirname($path));
        File::put($path, $content);

        return $this->relativePathFromSlug($slug);
    }

    public function relativePathFromSlug(string $slug): string
    {
        $absolutePath = $this->absolutePathFromSlug($slug);
        $projectRoot = rtrim(base_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return Str::after($absolutePath, $projectRoot);
    }

    protected function absolutePathFromSlug(string $slug): string
    {
        $relative = str_replace('.', DIRECTORY_SEPARATOR, strtolower($slug)) . '.md';

        return rtrim($this->basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relative;
    }
}
