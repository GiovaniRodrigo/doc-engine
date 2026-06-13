<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class MultilingualSupportTest extends TestCase
{
    #[Test]
    public function slash_slugs_are_normalized_to_dot_notation(): void
    {
        $this->createDocument('pt.guia-instalacao', '# Guia em Portugues');

        // Acessa a rota usando barra
        $response = $this->get('/docs/pt/guia-instalacao');

        $response->assertStatus(200);
        $response->assertSee('Guia em Portugues');
    }

    #[Test]
    public function index_shows_only_selected_language_documents_and_global_documents(): void
    {
        $this->createDocument('pt.guia', '# Guia PT');
        $this->createDocument('en.guia', '# Guia EN');
        $this->createDocument('sobre-nos', '# Sobre Nos');

        // Filtra por PT
        $response = $this->get('/docs?lang=pt');

        $response->assertStatus(200);
        $response->assertSee('Guia PT');
        $response->assertSee('Sobre Nos');
        $response->assertDontSee('Guia EN');

        // Filtra por EN
        $response = $this->get('/docs?lang=en');

        $response->assertStatus(200);
        $response->assertSee('Guia EN');
        $response->assertSee('Sobre Nos');
        $response->assertDontSee('Guia PT');
    }

    #[Test]
    public function language_preference_persists_in_session(): void
    {
        $this->createDocument('pt.guia', '# Guia PT');
        $this->createDocument('en.guia', '# Guia EN');

        // Define idioma via query
        $this->get('/docs?lang=en');

        // Acessa novamente sem query parameter
        $response = $this->get('/docs');

        // Deve persistir o idioma EN
        $response->assertStatus(200);
        $response->assertSee('Guia EN');
        $response->assertDontSee('Guia PT');
    }

    #[Test]
    public function show_page_provides_translations_data_map(): void
    {
        $this->createDocument('pt.guia-instalacao', '# Guia PT');
        $this->createDocument('en.guia-instalacao', '# Guia EN');
        $this->createDocument('es.guia-instalacao', '# Guia ES');

        $response = $this->get('/docs/pt.guia-instalacao');

        $response->assertStatus(200);

        // Verifica se as traducoes alternativas foram mapeadas no view data
        $viewData = $response->original->getData();
        $this->assertArrayHasKey('translations', $viewData);
        
        $translations = $viewData['translations'];
        $this->assertArrayHasKey('en', $translations);
        $this->assertArrayHasKey('es', $translations);
        $this->assertStringContainsString('en.guia-instalacao', $translations['en']);
        $this->assertStringContainsString('es.guia-instalacao', $translations['es']);
        // Nao deve conter a si mesma
        $this->assertArrayNotHasKey('pt', $translations);
    }

    #[Test]
    public function reading_localized_document_updates_session_language(): void
    {
        $this->createDocument('pt.guia', '# Guia PT');
        $this->createDocument('en.guia', '# Guia EN');

        // Define idioma da sessao inicial como PT
        session(['docs_language' => 'pt']);

        // Acessa um documento em EN
        $this->get('/docs/en.guia');

        // O idioma da sessao deve ter sido atualizado para EN
        $this->assertEquals('en', session('docs_language'));
    }
}
