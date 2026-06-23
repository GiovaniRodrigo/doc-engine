<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FormattingToolsTest extends TestCase
{
    #[Test]
    public function edit_page_contains_the_formatting_toolbar(): void
    {
        $this->createDocument('guia-formato', '# Guia');

        $response = $this->get('/docs/guia-formato/edit');

        $response->assertStatus(200);
        
        // Verifica se a barra de atalhos e os botoes principais estao na pagina
        $response->assertSee('docs-editor-toolbar');
        $response->assertSee('insertMarkdown(\'bold\')', false);
        $response->assertSee('insertMarkdown(\'mermaid\')', false);
        $response->assertSee('insertMarkdown(\'table\')', false);
    }

    #[Test]
    public function show_page_includes_mermaid_code_blocks_for_js_rendering(): void
    {
        $mermaidContent = "```mermaid\ngraph TD\n    A[Inicio] --> B[Fim]\n```";
        $this->createDocument('guia-diagrama', "# Titulo\n\n{$mermaidContent}");

        $response = $this->get('/docs/guia-diagrama');

        $response->assertStatus(200);
        
        // O conversor de Markdown deve gerar a classe css apropriada
        $response->assertSee('language-mermaid');
        $response->assertSee('graph TD');
        $response->assertSee('A[Inicio] --&gt; B[Fim]', false);
    }

    #[Test]
    public function edit_page_contains_uml_diagrams_dropdown_options(): void
    {
        $this->createDocument('guia-formato-uml', '# Guia');

        $response = $this->get('/docs/guia-formato-uml/edit');

        $response->assertStatus(200);

        // Verifica a presença do container do dropdown e as chamadas dos templates de diagramas UML
        $response->assertSee('docs-dropdown-container');
        $response->assertSee('insertMarkdown(\'mermaid\', \'sequence\')', false);
        $response->assertSee('insertMarkdown(\'mermaid\', \'class\')', false);
        $response->assertSee('insertMarkdown(\'mermaid\', \'usecase\')', false);
        $response->assertSee('insertMarkdown(\'mermaid\', \'state\')', false);
    }
}
