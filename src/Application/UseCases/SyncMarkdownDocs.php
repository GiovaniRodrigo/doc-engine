<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;
use Illuminate\Support\Str;

class SyncMarkdownDocs
{
    public function __construct(
        private FilesystemMarkdownStorage $storage,
        private DocumentRepository $repository,
        private GitVersionResolver $git
    ) {}

    public function execute(): int
    {
        try {
            $this->git->pull();
        } catch (\Exception $e) {
            // Log or ignore if git is not available or configured
        }

        $latestCommit = null;
        try {
            $latestCommit = $this->git->getLatestCommitHash();
        } catch (\Exception $e) {
        }

        $changedFiles = [];
        try {
            $changedFiles = $this->git->getChangedFiles();
        } catch (\Exception $e) {
        }

        $docsPath = config('documentation-engine.docs_path', 'docs');
        
        if (!empty($changedFiles)) {
            $relevantPaths = collect($changedFiles)
                ->filter(fn($f) => str_starts_with($f['path'], $docsPath))
                ->map(fn($f) => Str::after($f['path'], $docsPath . '/'))
                ->toArray();
            
            $files = $this->storage->getFiles($relevantPaths);
        } else {
            $files = $this->storage->all();
        }

        $count = 0;

        foreach ($files as $file) {
            $content = $file['content'];
            $checksum = md5($content);
            $slug = strtolower(trim($file['slug']));

            $document = $this->repository->findBySlug($slug);

            if (!$document) {
                $document = new Document(
                    id: (string) Str::uuid(),
                    slug: $slug,
                    title: ucfirst(str_replace('.', ' ', $slug))
                );

                $this->repository->save($document);
            }

            $latestVersion = $this->repository->latestVersion($document->id);

            if ($latestVersion && $latestVersion->checksum === $checksum) {
                continue;
            }

            $this->repository->createVersion(
                documentId: $document->id,
                content: $content,
                checksum: $checksum,
                gitCommit: $latestCommit
            );

            // Invalidate cache
            cache()->forget("doc_render_{$slug}");

            $count++;
        }

        $this->generateSummary();

        return $count;
    }

    private function generateSummary(): void
    {
        $files = $this->storage->all();
        
        if (empty($files)) {
            return;
        }

        $summary = "# Sumário da Documentação\n\n";
        $lastDir = '';

        foreach ($files as $file) {
            $relative = $file['relative'];
            
            // Ignora o próprio sumário para evitar recursão infinita no sync
            if ($file['slug'] === 'summary') continue;

            $dir = dirname($relative);
            
            if ($dir !== '.' && $dir !== $lastDir) {
                $summary .= "\n## " . ucfirst(str_replace(['/', '_'], ' ', $dir)) . "\n";
                $lastDir = $dir;
            }

            $name = basename($relative, '.md');
            $title = ucfirst(str_replace(['-', '_'], ' ', $name));
            $summary .= "- [{$title}](/docs/{$file['slug']})\n";
        }

        $this->storage->putBySlug('summary', $summary);
    }
}
