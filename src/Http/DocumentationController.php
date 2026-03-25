<?php

namespace Giovani\DocumentationEngine\Http;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Giovani\DocumentationEngine\Application\UseCases\UpdateDocument;
use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;
use Giovani\DocumentationEngine\Application\Services\SidebarBuilder;
use Giovani\DocumentationEngine\Infrastructure\Persistence\EloquentDocumentRepository;
use Giovani\DocumentationEngine\Application\Services\BreadcrumbBuilder;
use Giovani\DocumentationEngine\Application\Services\NavigationBuilder;
use Giovani\DocumentationEngine\Infrastructure\Storage\FilesystemMarkdownStorage;
use Giovani\DocumentationEngine\Infrastructure\Git\GitVersionResolver;

class DocumentationController extends Controller
{
    public function show($slug)
    {
        $repo = new EloquentDocumentRepository();
        $doc = (new ShowDocument($repo))->execute($slug);

        abort_if(!$doc, 404);

        $renderer = new MarkdownRenderer();
        $allSlugs = $repo->allSlugs();

        return view('documentation-engine::show', [
            'html' => $renderer->render($doc->content),
            'sidebar' => $this->buildSidebar($allSlugs),
            'breadcrumb' => (new BreadcrumbBuilder())->build($slug),
            'nav' => (new NavigationBuilder())->build($allSlugs, $slug),
            'slug' => $slug,
        ]);
    }

    public function edit(string $slug)
    {
        $repo = new EloquentDocumentRepository();
        $doc = (new ShowDocument($repo))->execute($slug);

        abort_if(!$doc, 404);

        $allSlugs = $repo->allSlugs();

        return view('documentation-engine::edit', [
            'slug' => $slug,
            'content' => $doc->content,
            'sidebar' => $this->buildSidebar($allSlugs),
            'breadcrumb' => (new BreadcrumbBuilder())->build($slug),
        ]);
    }

    public function update(Request $request, string $slug)
    {
        $repo = new EloquentDocumentRepository();
        $doc = (new ShowDocument($repo))->execute($slug);

        abort_if(!$doc, 404);

        $data = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $storage = new FilesystemMarkdownStorage(
            base_path(config('documentation-engine.docs_path'))
        );

        (new UpdateDocument($storage, new GitVersionResolver()))
            ->execute($slug, $data['content']);

        return redirect("/docs/{$slug}");
    }

    protected function buildSidebar(array $slugs): array
    {
        return (new SidebarBuilder())->build($slugs);
    }
}
