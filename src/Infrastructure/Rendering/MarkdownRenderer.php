<?php

namespace Giovani\DocumentationEngine\Infrastructure\Rendering;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Table\TableExtension;

class MarkdownRenderer
{
    private MarkdownConverter $converter;

    public function __construct()
    {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new AttributesExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public function render(string $markdown): string
    {
        $html = $this->converter->convert($markdown)->getContent();
        
        return $this->processCallouts($html);
    }

    private function processCallouts(string $html): string
    {
        // GitHub-style alerts: > [!NOTE], > [!TIP], > [!IMPORTANT], > [!WARNING], > [!CAUTION]
        $patterns = [
            'NOTE' => 'info',
            'TIP' => 'success',
            'IMPORTANT' => 'primary',
            'WARNING' => 'warning',
            'CAUTION' => 'danger'
        ];

        foreach ($patterns as $type => $class) {
            $pattern = '/<blockquote>\s*<p>\s*\[\!' . $type . '\]/i';
            $replacement = '<blockquote class="callout callout-' . $class . '"><p>';
            $html = preg_replace($pattern, $replacement, $html);
        }

        return $html;
    }
}
