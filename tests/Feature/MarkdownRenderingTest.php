<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_renders_tables()
    {
        $markdown = "| Header |\n| --- |\n| Cell |";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<th>Header</th>', $html);
        $this->assertStringContainsString('<td>Cell</td>', $html);
    }

    #[Test]
    public function it_renders_callouts_alerts()
    {
        $markdown = "> [!NOTE]\n> This is a callout.";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('class="callout callout-info"', $html);
        $this->assertStringContainsString('This is a callout', $html);
    }

    #[Test]
    public function it_supports_syntax_highlighting()
    {
        $markdown = "```php\necho 'hello';\n```";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('class="language-php"', $html);
    }

    #[Test]
    public function it_adds_stable_heading_anchors()
    {
        $html = $this->renderer->render("## Primeiros passos\n\n### Instalação");

        $this->assertStringContainsString('<h2 id="primeiros-passos">Primeiros passos</h2>', $html);
        $this->assertStringContainsString('<h3 id="instalacao">Instalação</h3>', $html);
    }
}
