<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createDocument('guia', '# Guia');
    }

    #[Test]
    public function it_caches_rendered_html()
    {
        $slug = 'guia';

        Cache::shouldReceive('remember')
            ->once()
            ->with("doc_render_{$slug}", \Mockery::any(), \Mockery::any())
            ->andReturn('<h1>Rendered Content</h1>');

        Cache::shouldReceive('remember')
            ->with('doc_slugs_all', \Mockery::any(), \Mockery::any())
            ->andReturn([$slug]);

        Cache::shouldReceive('remember')
            ->with('doc_sidebar_all', \Mockery::any(), \Mockery::any())
            ->andReturn([]);

        $response = $this->get("/docs/{$slug}");
        $response->assertStatus(200);
        $response->assertSee('Rendered Content');
    }

    #[Test]
    public function it_does_not_invalidate_published_cache_when_saving_a_draft()
    {
        $slug = 'guia';

        Cache::spy();

        $response = $this->put("/docs/{$slug}", [
            'content' => '# Novo Conteudo',
        ]);

        $response->assertRedirect("/docs/{$slug}/edit");

        Cache::shouldNotHaveReceived('forget');
    }
}
