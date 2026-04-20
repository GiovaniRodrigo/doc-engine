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
        $markdown = "| Header |\n| --- |\n| Cell |";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('<table>', $html);
        $this->assertStringContainsString('<th>Header</th>', $html);
        $this->assertStringContainsString('<td>Cell</td>', $html);
    }

    /** @test */
    public function it_renders_callouts_alerts()
    {
        $markdown = "> [!NOTE]\n> This is a callout.";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('class="callout callout-info"', $html);
        $this->assertStringContainsString('This is a callout', $html);
    }

    /** @test */
    public function it_supports_syntax_highlighting()
    {
        $markdown = "```php\necho 'hello';\n```";
        $html = $this->renderer->render($markdown);

        $this->assertStringContainsString('class="language-php"', $html);
    }
}
