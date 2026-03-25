<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Application\UseCases;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationPlatformEngine\Publishing\Domain\PublicationStateMachine;
use RuntimeException;

class PublishDocumentation
{
    public function __construct(
        private DocumentRepository $repository,
        private PublicationStateMachine $stateMachine,
    ) {}

    public function execute(
        string $project,
        string $slug
    ): void {

        $version = $this->repository
            ->findLatestVersionBySlug($project, $slug);

        if (! $version) {
            throw new RuntimeException(
                "Document version not found for {$slug}"
            );
        }

        // aplica transição de estado
        $this->stateMachine->publish($version);

        // define data de publicação
        $version->publishedAt = now();

        // persiste
        $this->repository->saveVersion($version);
    }
}