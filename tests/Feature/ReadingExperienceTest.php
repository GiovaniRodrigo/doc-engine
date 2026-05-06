<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;

class ReadingExperienceTest extends TestCase
{
    #[Test]
    public function docs_index_shows_empty_state_when_there_are_no_documents(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('Nenhum documento encontrado');
        $response->assertSee('php artisan docs:sync');
    }

    #[Test]
    public function missing_document_shows_friendly_not_found_page(): void
    {
        $this->createDocument('guia', '# Guia');

        $response = $this->get('/docs/nao-existe');

        $response->assertStatus(404);
        $response->assertSee('Documento não encontrado');
        $response->assertSee('nao-existe');
    }

    #[Test]
    public function show_page_includes_table_of_contents_from_headings(): void
    {
        $this->createDocument('guia', "# Guia\n\n## Instalação\n\n### Composer");

        $response = $this->get('/docs/guia');

        $response->assertStatus(200);
        $response->assertSee('Nesta página');
        $response->assertSee('href="#instalacao"', false);
        $response->assertSee('id="composer"', false);
    }

    #[Test]
    public function search_finds_documents_by_title_slug_and_content(): void
    {
        $this->createDocument('guia', '# Guia de instalação');
        $this->createDocument('api.referencia', '# Endpoints públicos');

        $response = $this->get('/docs/search?q=publicos');

        $response->assertStatus(200);
        $response->assertSee('api.referencia');

        $response = $this->get('/docs/search?q=guia');

        $response->assertStatus(200);
        $response->assertSee('guia');
    }

    #[Test]
    public function search_does_not_return_archived_documents(): void
    {
        $this->createDocument('guia', '# Conteúdo legado');

        DB::table('documents')
            ->where('slug', 'guia')
            ->update(['state' => 'archived']);

        $response = $this->get('/docs/search?q=legado');

        $response->assertStatus(200);
        $response->assertSee('Sem resultados');
        $response->assertDontSee('guia');
    }
}
