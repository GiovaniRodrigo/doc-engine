<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Application\UseCases;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Aggregates\DocumentAggregate;
use Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationPlatformEngine\SourceControl\Domain\Services\RepositoryDiffService;

class SyncDocumentation
{
    public function __construct(
        private RepositoryDiffService $diff,
        private DocumentRepository $repository,
        private DocumentAggregate $aggregate
    ) {}

    public function execute(
        string $project,
        string $repoPath,
        string $commit
    ): void {

        $files = $this->diff->changedMarkdownFiles(
            $repoPath,
            $commit
        );

        foreach ($files as $file) {

            $markdown = file_get_contents(
                $repoPath.'/'.$file
            );

            $document = $this->repository
                ->findByProjectAndPath($project, $file);

            if (! $document) {
                continue;
            }

            $version = $this->aggregate->createVersion(
                $document,
                $markdown,
                $commit
            );

            $this->repository->saveVersion($version);
        }
    }
}