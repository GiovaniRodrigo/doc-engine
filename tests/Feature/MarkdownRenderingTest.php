<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Giovani\DocumentationEngine\Tests\TestCase;

class MarkdownRenderingTest extends TestCase
{
    private MarkdownRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->renderer = new MarkdownRenderer();
    }

    /** @test */
    public function it_renders_tables()
    {
        // RF10 - Suporte a tabelas
        $markdown = "| Header | \n | --- | \n | Cell |";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<th>Header</th>', $html);
        $this->assertStringContainsString('<td>Cell</td>', $html);
    }

    /** @test */
    public function it_renders_callouts_alerts()
    {
        // RF10 - Suporte a alertas (callouts)
        // Geralmente via extensões do commonmark
        $markdown = "> [!NOTE]\n> This is a callout.";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('class="callout"', $html, 'Callout rendering is missing.');
    }

    /** @test */
    public function it_supports_syntax_highlighting()
    {
        // RF10 - Syntax highlight
        $markdown = "```php\necho 'hello';\n```";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('class="language-php"', $html);
    }
}
