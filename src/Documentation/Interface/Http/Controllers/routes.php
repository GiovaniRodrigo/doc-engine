<?php

namespace Giovani\DocumentationPlatformEngine\Documentation\Interface\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationPlatformEngine\Documentation\Interface\Http\Controllers\ShowDocumentationController;

Route::prefix('docs')->group(function () {

    Route::get('{project}/{slug}', ShowDocumentationController::class);

});