<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Tests\Fixtures\RejectDocsMiddleware;
use Giovani\DocumentationEngine\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RouteMiddlewareTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('documentation-engine.middleware', [
            RejectDocsMiddleware::class,
        ]);
    }

    #[Test]
    public function docs_routes_use_the_configured_documentation_middleware(): void
    {
        $this->createDocument('guia', '# Guia');

        $this->get('/docs')->assertStatus(418);
        $this->get('/docs/search?q=guia')->assertStatus(418);
        $this->get('/docs/guia')->assertStatus(418);
        $this->get('/docs/guia/edit')->assertStatus(418);
    }
}
