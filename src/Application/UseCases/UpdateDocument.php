<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;

use Giovani\DocumentationEngine\Application\UseCases\SyncMarkdownDocs;

class UpdateDocument
{
    public function __construct(
        private FilesystemMarkdownStorage $storage,
        private GitVersionResolver $git,
        private SyncMarkdownDocs $sync
    ) {}

    public function execute(string $slug, string $content): void
    {
        $path = $this->storage->putBySlug($slug, $content);

        $this->git->commit($path, "update doc");

        // Sincroniza o banco imediatamente após o commit
        $this->sync->execute();
    }
}
