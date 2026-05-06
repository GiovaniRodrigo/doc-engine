<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;

class CustomLayoutRenderingTest extends TestCase
{
    private ?string $customResourcePath = null;

    protected function tearDown(): void
    {
        if ($this->customResourcePath !== null) {
            File::deleteDirectory($this->customResourcePath);
        }

        parent::tearDown();
    }

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

    public function test_documentation_views_use_project_customization_path(): void
    {
        $hints = app('view')->getFinder()->getHints()['documentation-engine'];

        $this->assertSame([
            resource_path('views/documentation-engine'),
            realpath(dirname(__DIR__, 2).'/resources/views'),
        ], $hints);
    }

    public function test_default_styles_prefer_custom_project_css_files(): void
    {
        $this->customResourcePath = resource_path('css/documentation-engine');
        File::ensureDirectoryExists(resource_path('css/documentation-engine/docs'));
        File::put(
            resource_path('css/documentation-engine/docs/themes.css'),
            '.docs-layout { --docs-custom-theme: 1; }'
        );

        $html = view('documentation-engine::partials.styles')->render();

        $this->assertStringContainsString('--docs-custom-theme: 1', $html);
        $this->assertStringNotContainsString('--docs-page-background: #f8fafc', $html);
    }
}
