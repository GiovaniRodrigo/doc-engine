<?php

namespace Giovani\DocumentationEngine\Infrastructure\Rendering;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\Table\TableExtension;
use Illuminate\Support\Str;

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
        
        return $this->addHeadingAnchors($this->processCallouts($html));
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

    private function addHeadingAnchors(string $html): string
    {
        return preg_replace_callback(
            '/<h([1-6])([^>]*)>(.*?)<\/h\1>/is',
            function (array $matches) {
                if (preg_match('/\sid=([\'"])[^\'"]+\1/i', $matches[2])) {
                    return $matches[0];
                }

                $title = trim(strip_tags($matches[3]));

                if ($title === '') {
                    return $matches[0];
                }

                $id = Str::slug($title);

                return "<h{$matches[1]} id=\"{$id}\"{$matches[2]}>{$matches[3]}</h{$matches[1]}>";
            },
            $html
        ) ?? $html;
    }
}
