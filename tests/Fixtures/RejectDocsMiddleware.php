<?php

namespace Giovani\DocumentationEngine\Tests\Fixtures;

use Closure;
use Illuminate\Http\Request;

class RejectDocsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        abort(418);
    }
}
