<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class CacheTest extends TestCase
{
    /** @test */
    public function it_caches_rendered_html()
    {
        $slug = 'guia';
        
        Cache::shouldReceive('remember')
            ->once()
            ->with("doc_render_{$slug}", \Mockery::any(), \Mockery::any())
            ->andReturn('<h1>Rendered Content</h1>');

        $response = $this->get("/docs/{$slug}");
        $response->assertStatus(200);
        $response->assertSee('Rendered Content');
    }

    /** @test */
    public function it_invalidates_cache_on_update()
    {
        $slug = 'guia';

        Cache::shouldReceive('forget')
            ->once()
            ->with("doc_render_{$slug}");

        $response = $this->put("/docs/{$slug}", [
            'content' => '# Novo Conteudo'
        ]);

        $response->assertRedirect("/docs/{$slug}");
    }
}
