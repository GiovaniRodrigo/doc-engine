<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Interface\Http\Controllers;

use Giovani\DocumentationPlatformEngine\Navigation\Application\BuildNavigation;
use Giovani\DocumentationPlatformEngine\Navigation\Application\BuildBreadcrumbs;
use Illuminate\Support\Str;

class ShowDocumentationController extends Controller
{

    public function __invoke(
        string $project,
        string $path = 'README',
        BuildNavigation $navBuilder,
        BuildBreadcrumbs $breadcrumbBuilder
    ) {

        $file = storage_path("docs/{$project}/{$path}.md");

        if (! file_exists($file)) {
            abort(404);
        }

        $html = Str::markdown(file_get_contents($file));

        return response()->view('documentation::show', [
            'html' => $html,
            'nav' => $navBuilder->build($project),
            'breadcrumbs' => $breadcrumbBuilder->build($path),
            'project' => $project,
            'path' => $path
        ]);
    }
}
