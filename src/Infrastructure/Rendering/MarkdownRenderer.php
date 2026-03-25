<?php

namespace Giovani\DocumentationEngine\Infrastructure\Rendering;

use League\CommonMark\CommonMarkConverter;

class MarkdownRenderer
{
    public function render(string $markdown): string
    {
        $converter = new CommonMarkConverter();
        return $converter->convert($markdown)->getContent();
    }
}
