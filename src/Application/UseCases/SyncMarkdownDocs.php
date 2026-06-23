<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Application\DTO\SyncResult;
use Giovani\DocumentationEngine\Domain\Entities\Document;
use Giovani\DocumentationEngine\Domain\Events\DocumentSynced;
use Giovani\DocumentationEngine\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncMarkdownDocs
{
    public function __construct(
        private FilesystemMarkdownStorage $storage,
        private DocumentRepository $repository,
        private GitVersionResolver $git
    ) {}

    public function execute(?string $project = null, bool $dryRun = false): SyncResult
    {
        $start = microtime(true);
        $result = new SyncResult(dryRun: $dryRun);

        if (! $dryRun) {
            try {
                $this->git->pull();
            } catch (\Exception $e) {
                $result->errors[] = 'Git pull failed: '.$e->getMessage();
            }
        }

        $latestCommit = null;
        try {
            $latestCommit = $this->git->getLatestCommitHash();
        } catch (\Exception $e) {
            $result->errors[] = 'Git commit detection failed: '.$e->getMessage();
        }

        $result->commit = $latestCommit;

        try {
            $this->git->getChangedFiles();
        } catch (\Exception) {
            // Ignored for now as we perform a full scan anyway
        }

        $files = $this->storage->all();
        $currentSlugs = [];

        foreach ($files as $file) {
            $relative = $file['relative'] ?? $file['path'] ?? '';
            $result->readFiles[] = $relative;

            if (isset($file['path']) && ! is_readable($file['path'])) {
                $result->ignoredFiles[] = $relative;
                $result->errors[] = "File is not readable: {$relative}";

                continue;
            }

            $content = $file['content'];
            $checksum = md5($content);
            $slug = $this->slugFromRelative($file['relative'] ?? '', $project, strtolower(trim($file['slug'])));

            if ($slug === 'summary' || str_ends_with($slug, '.summary')) {
                $result->ignoredFiles[] = $relative;

                continue;
            }

            $currentSlugs[] = $slug;

            $document = $this->repository->findBySlug($slug);

            if (! $document) {
                $document = new Document(
                    id: (string) Str::uuid(),
                    slug: $slug,
                    title: ucfirst(str_replace('.', ' ', $slug))
                );

                if (! $dryRun) {
                    $this->repository->save($document);
                }
            } elseif ($document->state === 'archived' && ! $dryRun) {
                $this->repository->activateBySlug($slug);
            }

            $latestVersion = $this->repository->latestPublishedVersion($document->id);

            if ($latestVersion && $latestVersion->checksum === $checksum) {
                $result->ignoredFiles[] = $relative;

                continue;
            }

            if (! $dryRun) {
                $this->repository->createVersion(
                    documentId: $document->id,
                    content: $content,
                    checksum: $checksum,
                    gitCommit: $latestCommit
                );

                cache()->forget("doc_render_{$slug}");
            }

            $result->changedFiles[] = $relative;
            $result->createdVersions[] = $slug;
        }

        $slugsToArchive = array_values(array_diff(
            $this->repository->allActiveSlugs($project ? strtolower($project) : null),
            $currentSlugs
        ));

        foreach ($slugsToArchive as $slug) {
            if (! $dryRun) {
                $this->repository->archiveBySlug($slug);
                cache()->forget("doc_render_{$slug}");
            }

            $result->archivedDocuments[] = $slug;
        }

        if (! $dryRun) {
            $this->generateSummary($project);

            $projectKey = $project ? strtolower($project) : 'all';
            cache()->forget("doc_slugs_{$projectKey}");
            cache()->forget("doc_sidebar_{$projectKey}");
        }

        $end = microtime(true);
        Log::info(sprintf(
            'Sync completed in %.4fms (changes: %d, versions: %d, archived: %d)',
            ($end - $start) * 1000,
            count($result->changedFiles),
            count($result->createdVersions),
            count($result->archivedDocuments)
        ));

        if (! $dryRun) {
            Event::dispatch(new DocumentSynced(
                created: count($result->createdVersions),
                updated: count($result->changedFiles),
                skipped: count($result->ignoredFiles),
                commit: $result->commit,
            ));
        }

        return $result;
    }

    private function slugFromRelative(string $relative, ?string $project = null, ?string $fallback = null): string
    {
        $slug = $fallback ?: str_replace(
            ['.md', '/', '\\'],
            ['', '.', '.'],
            strtolower($relative)
        );

        $slug = strtolower(trim($slug));

        if (! $project) {
            return $slug;
        }

        $project = strtolower($project);

        return $slug === 'readme'
            ? $project
            : "{$project}.{$slug}";
    }

    private function generateSummary(?string $project = null): void
    {
        $files = $this->storage->all();

        if (empty($files)) {
            return;
        }

        $summary = "# Sumário da Documentação\n\n";
        if ($project) {
            $summary = '# Sumário: '.ucfirst($project)."\n\n";
        }

        $lastDir = '';

        foreach ($files as $file) {
            $relative = $file['relative'];
            $slug = $file['slug'];

            // Ignora sumários gerados para evitar recursão entre execuções.
            if ($slug === 'summary' || str_ends_with($slug, '.summary')) {
                continue;
            }

            if ($project) {
                if ($slug === 'readme') {
                    $slug = strtolower($project);
                } else {
                    $slug = strtolower($project).'.'.$slug;
                }
            }

            $dir = dirname($relative);

            if ($dir !== '.' && $dir !== $lastDir) {
                $summary .= "\n## ".ucfirst(str_replace(['/', '_'], ' ', $dir))."\n";
                $lastDir = $dir;
            }

            $name = basename($relative, '.md');
            $title = ucfirst(str_replace(['-', '_'], ' ', $name));
            $summary .= "- [{$title}](/docs/{$slug})\n";
        }

        $summarySlug = $project ? $project.'.summary' : 'summary';
        $this->storage->putBySlug($summarySlug, $summary);
    }
}
