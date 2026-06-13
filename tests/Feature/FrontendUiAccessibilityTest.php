<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\File;

class FrontendUiAccessibilityTest extends TestCase
{
    public function test_default_layout_includes_accessible_mobile_menu_button(): void
    {
        $html = view('documentation-engine::layouts.default', [
            'sidebar' => [],
            'query' => '',
            'content' => '<h1>Conteudo</h1>',
        ])->render();

        // Verifica a presença do botão de menu com os atributos ARIA adequados
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('aria-controls="docs-sidebar"', $html);
        $this->assertStringContainsString('aria-label="Abrir menu de navegação"', $html);
        $this->assertStringContainsString('docs-mobile-toggle', $html);
        
        // E o script JS de interatividade
        $this->assertStringContainsString('document.getElementById(\'docs-mobile-toggle\')', $html);
    }

    public function test_compiled_styles_contain_hsl_theme_variables(): void
    {
        $html = view('documentation-engine::partials.styles')->render();

        // Deve conter variáveis baseadas em HSL e não apenas os antigos hexadecimais brutos
        $this->assertStringContainsString('--docs-accent: hsl(', $html);
        $this->assertStringContainsString('--docs-page-background: hsl(', $html);
        $this->assertStringContainsString('--docs-text: hsl(', $html);
        $this->assertStringContainsString('--docs-border: hsl(', $html);
        
        // E deve possuir o suporte a focus-visible
        $this->assertStringContainsString(':focus-visible', $html);
    }

    public function test_layout_blade_includes_base_blade_to_prevent_duplication(): void
    {
        $layoutPath = resource_path('views/documentation-engine/layout.blade.php');
        if (!File::exists($layoutPath)) {
            $layoutPath = dirname(__DIR__, 2) . '/resources/views/layout.blade.php';
        }

        $content = File::get($layoutPath);

        // Deve incluir a view base ao invés de duplicar a estrutura HTML inteira
        $this->assertStringContainsString("include('documentation-engine::base')", $content);
        $this->assertStringNotContainsString('<!DOCTYPE html>', $content);
    }
}
