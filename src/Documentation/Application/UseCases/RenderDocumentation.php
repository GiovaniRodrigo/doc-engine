<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Application\UseCases;

use Giovani\DocumentationPlatformEngine\Documentation\Domain\Repositories\DocumentRepository;
use Giovani\DocumentationPlatformEngine\Rendering\Application\Services\Renderer;

class RenderDocumentation
{
    public function __construct(
        private DocumentRepository $repository,
        private Renderer $renderer
    ) {}

    public function renderFile(string $file): string
    {
        $document = $this->repository->findByPath($file);

        if (! $document) {
            abort(404, 'Documento não encontrado');
        }

        return $this->renderer->render(
            $document->getContent()
        );
    }
}