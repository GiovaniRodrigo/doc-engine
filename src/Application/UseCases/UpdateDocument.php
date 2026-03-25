<?php

namespace Giovani\DocumentationEngine\Application\UseCases;

use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;

class UpdateDocument
{
    public function __construct(
        private FilesystemMarkdownStorage $storage,
        private GitVersionResolver $git
    ) {}

    public function execute(string $slug, string $content): void
    {
        $path = $this->storage->putBySlug($slug, $content);

        $this->git->commit($path, "update doc");
    }
}
