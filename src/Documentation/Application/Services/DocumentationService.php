<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Application\Services;

use Giovani\DocumentationPlatformEngine\Documentation\Application\UseCases\SyncDocumentation;
use Giovani\DocumentationPlatformEngine\Documentation\Application\UseCases\RenderDocumentation;

class DocumentationService
{
    public function __construct(
        private SyncDocumentation $syncDocumentation,
        private RenderDocumentation $renderDocumentation,
    ) {}

    /**
     * Sincroniza documentação a partir de um commit específico
     */
    public function sync(
        string $project,
        string $repositoryPath,
        string $commit
    ): void {
        $this->syncDocumentation->execute(
            project: $project,
            repoPath: $repositoryPath,
            commit: $commit
        );
    }

    /**
     * Renderiza documentação pendente
     */
    public function render(): void
    {
        $this->renderDocumentation->execute();
    }

    /**
     * Pipeline completo simples (V1)
     */
    public function syncAndRender(
        string $project,
        string $repositoryPath,
        string $commit
    ): void {

        $this->sync(
            project: $project,
            repositoryPath: $repositoryPath,
            commit: $commit
        );

        $this->render();
    }
}