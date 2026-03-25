<?php

namespace Giovani\DocumentationEngine\Http;

use Illuminate\Routing\Controller;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Giovani\DocumentationEngine\Infrastructure\Persistence\EloquentDocumentRepository;
use Giovani\DocumentationEngine\Infrastructure\Rendering\MarkdownRenderer;

class DocumentationController extends Controller
{
    public function show($slug)
    {
        $repo = new EloquentDocumentRepository();

        $usecase = new ShowDocument($repo);

        $doc = $usecase->execute($slug);

        abort_if(!$doc, 404);

        $renderer = new MarkdownRenderer();

        return view('documentation-engine::show', [
            'html' => $renderer->render($doc->content)
        ]);
    }
}
