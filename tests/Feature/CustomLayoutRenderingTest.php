<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;

class CustomLayoutRenderingTest extends TestCase
{
    public function test_show_view_uses_the_configured_custom_layout(): void
    {
        config()->set('documentation-engine.layout', 'layouts.custom');

        $html = view('documentation-engine::show', [
            'html' => '<h1>Conteudo</h1>',
            'sidebar' => [],
            'breadcrumb' => [],
            'nav' => ['prev' => null, 'next' => null],
            'slug' => 'docs/guia',
        ])->render();

        $this->assertStringContainsString('data-test-layout="custom"', $html);
        $this->assertStringContainsString('<h1>Conteudo</h1>', $html);
    }

    public function test_edit_view_uses_the_configured_custom_layout(): void
    {
        config()->set('documentation-engine.layout', 'layouts.custom');
        config()->set('documentation-engine.ai.enabled', false);

        $html = view('documentation-engine::edit', [
            'slug' => 'docs/guia',
            'content' => '# Titulo',
            'sidebar' => [],
            'breadcrumb' => [],
        ])->render();

        $this->assertStringContainsString('data-test-layout="custom"', $html);
        $this->assertStringContainsString('Editar documento', $html);
        $this->assertStringContainsString('# Titulo', $html);
    }
}
