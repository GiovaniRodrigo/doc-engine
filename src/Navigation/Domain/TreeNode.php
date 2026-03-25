<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Interface\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class ShowDocumentationController extends Controller
{
    public function __invoke(string $project, string $path = 'README')
    {
        $path = trim($path, '/');

        $file = storage_path(
            "docs/{$project}/{$path}.md"
        );

        if (! file_exists($file)) {
            abort(404);
        }

        $html = Str::markdown(
            file_get_contents($file)
        );

        return response()->view('documentation::show', [
            'html' => $html,
            'path' => $path,
            'project' => $project,
        ]);
    }
}