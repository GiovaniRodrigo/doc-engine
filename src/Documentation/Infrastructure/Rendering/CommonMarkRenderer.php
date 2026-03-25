<?php

namespace Giovani\DocumentationPlatformEngine\Rendering\Infrastructure;

use League\CommonMark\CommonMarkConverter;
use Giovani\DocumentationPlatformEngine\Rendering\Application\Services\Renderer;

class CommonMarkRenderer implements Renderer
{
    public function __construct(
        private CommonMarkConverter $converter
    ) {}

    public function render(string $markdown): string
    {
        return $this->converter
            ->convert($markdown)
            ->getContent();
    }
}
