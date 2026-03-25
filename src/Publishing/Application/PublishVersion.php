<?php

namespace Giovani\DocumentationPlatformEngine\Publishing\Application;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationPlatformEngine\Publishing\Domain\PublicationStateMachine;

class PublishVersion
{
    public function __construct(
        private DocumentRepository $repository,
        private PublicationStateMachine $machine
    ) {}

    public function execute(
        string $project,
        string $slug
    ): void {

        $version = $this->repository
            ->findLatestVersionBySlug($project, $slug);

        if (! $version) {
            return;
        }

        $this->machine->publish($version);

        $this->repository->saveVersion($version);
    }
}