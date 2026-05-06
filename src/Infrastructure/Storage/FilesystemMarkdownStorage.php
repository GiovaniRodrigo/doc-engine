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
        if (! is_dir($this->basePath)) {
            return [];
        }

        return collect(File::allFiles($this->basePath))
            ->filter(fn ($file) => strtolower($file->getExtension()) === 'md')
            ->map(function ($file) {

                $absolute = $file->getRealPath();

                $relative = str_replace(
                    $this->basePath.DIRECTORY_SEPARATOR,
                    '',
                    $absolute
                );

                return [
                    'path' => $absolute,
                    'relative' => $relative,
                    'slug' => $this->slugFromRelative($relative),
                    'content' => $this->readAsUtf8($absolute),
                ];
            })
            ->values()
            ->toArray();
    }

    protected function readAsUtf8(string $path): string
    {
        $content = File::get($path);
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'ASCII'], true);

        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        return $content;
    }

    protected function slugFromRelative(string $relative): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, str_replace(['.md', '/', '\\'], ['', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $relative));
        
        return collect($parts)
            ->map(fn ($part) => Str::slug($part, '.'))
            ->filter()
            ->implode('.');
    }

    public function getBySlug(string $slug): ?string
    {
        $path = $this->absolutePathFromSlug($slug);

        if (! File::exists($path)) {
            return null;
        }

        return $this->readAsUtf8($path);
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
        $projectRoot = rtrim(base_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return Str::after($absolutePath, $projectRoot);
    }

    public function getDocsPath(): string
    {
        return $this->basePath;
    }

    public function getRelativeDocsPath(): string
    {
        $projectRoot = rtrim(base_path(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        return Str::after($this->basePath, $projectRoot);
    }

    public function getFiles(array $paths): array
    {
        return collect($paths)
            ->filter(fn ($path) => strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'md')
            ->map(function ($path) {
                $absolute = rtrim($this->basePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$path;

                if (! File::exists($absolute)) {
                    return null;
                }

                return [
                    'path' => $absolute,
                    'relative' => $path,
                    'slug' => $this->slugFromRelative($path),
                    'content' => $this->readAsUtf8($absolute),
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }

    protected function absolutePathFromSlug(string $slug): string
    {
        $relative = str_replace('.', DIRECTORY_SEPARATOR, strtolower($slug)).'.md';

        return rtrim($this->basePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$relative;
    }
}
