<?php

namespace Giovani\DocumentationEngine\Http;

use Illuminate\Routing\Controller;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Giovani\DocumentationEngine\Application\Services\SidebarBuilder;
use Giovani\DocumentationEngine\Infrastructure\Persistence\EloquentDocumentRepository;
use Giovani\DocumentationEngine\Application\Services\BreadcrumbBuilder;
use Giovani\DocumentationEngine\Application\Services\NavigationBuilder;

class DocumentationController extends Controller
{
    public function show($slug)
    {
        $repo = new EloquentDocumentRepository();

        $usecase = new ShowDocument($repo);

        $doc = $usecase->execute($slug);

        abort_if(!$doc, 404);

        $renderer = new MarkdownRenderer();
        $builder = new SidebarBuilder();

        $sidebar = $builder->build(
            $repo->allSlugs()
        );

        $allSlugs = $repo->allSlugs();

        $breadcrumb = (new BreadcrumbBuilder())->build($slug);

        $nav = (new NavigationBuilder())->build($allSlugs, $slug);

        return view('documentation-engine::show', [
            'html' => $renderer->render($doc->content),
            'sidebar' => $sidebar,
            'breadcrumb' => $breadcrumb,
            'nav' => $nav,
        ]);
    }
}
