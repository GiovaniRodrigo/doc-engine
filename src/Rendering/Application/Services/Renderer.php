<?php

namespace Giovani\DocumentationPlatformEngine\Rendering\Application\Services;

interface Renderer
{
    public function render(string $markdown): string;
}
