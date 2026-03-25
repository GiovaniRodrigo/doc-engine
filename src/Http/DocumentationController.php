<?php

namespace Giovani\DocumentationEngine\Http;

use Throwable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Container\BindingResolutionException;
use Giovani\DocumentationEngine\Application\UseCases\ShowDocument;
use Giovani\DocumentationEngine\Application\UseCases\GenerateWithAI;
use Giovani\DocumentationEngine\Application\UseCases\UpdateDocument;
use Giovani\DocumentationEngine\Infrastructure\AI\AiProvider;
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

    public function generate(Request $request, string $slug): JsonResponse
    {
        abort_unless(config('documentation-engine.ai.enabled', true), 404);

        $repo = new EloquentDocumentRepository();
        $doc = (new ShowDocument($repo))->execute($slug);

        abort_if(!$doc, 404);

        $data = $request->validate([
            'prompt' => ['required', 'string'],
            'content' => ['nullable', 'string'],
        ]);

        try {
            $provider = $this->resolveAiProvider();
        } catch (BindingResolutionException) {
            return response()->json([
                'message' => 'Nenhum provedor de IA foi configurado para a documentação.',
            ], 500);
        }

        try {
            $generated = (new GenerateWithAI($provider))->execute(
                $this->buildAiPrompt($slug, $data['prompt'], $data['content'] ?? $doc->content)
            );
        } catch (Throwable $exception) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Não foi possível gerar conteúdo com IA.',
            ], 500);
        }

        return response()->json([
            'content' => $generated,
        ]);
    }

    protected function buildSidebar(array $slugs): array
    {
        return (new SidebarBuilder())->build($slugs);
    }

    protected function resolveAiProvider(): AiProvider
    {
        return app(AiProvider::class);
    }

    protected function buildAiPrompt(string $slug, string $instruction, string $content): string
    {
        return implode("\n\n", [
            'Voce esta editando uma documentacao em Markdown.',
            "Slug do documento: {$slug}",
            'Tarefa do usuario:',
            trim($instruction),
            'Conteudo atual do documento:',
            trim($content) !== '' ? $content : '[vazio]',
            'Responda apenas com o Markdown final, sem explicacoes extras.',
        ]);
    }
}
