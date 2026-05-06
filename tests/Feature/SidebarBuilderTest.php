<?php

namespace Giovani\DocumentationEngine\Tests\Feature;

use Giovani\DocumentationEngine\Application\Services\SidebarBuilder;
use Giovani\DocumentationEngine\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class SidebarBuilderTest extends TestCase
{
    #[Test]
    public function adr_documents_are_rendered_as_single_readable_sidebar_items(): void
    {
        $sidebar = app(SidebarBuilder::class)->build([
            'arquitetura.decisoes.adr-001-laravel-octane-swoole',
        ]);

        $this->assertSame('Arquitetura', $sidebar[0]->title);
        $this->assertSame('Decisoes', $sidebar[0]->children[0]->title);

        $adr = $sidebar[0]->children[0]->children[0];

        $this->assertSame('ADR 001 Laravel', $adr->title);
        $this->assertSame('arquitetura.decisoes.adr-001-laravel-octane-swoole', $adr->slug);
        $this->assertCount(0, $adr->children);
    }
}
