<?php

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationEngine\Http\DocumentationController;

Route::get('/docs/{slug}', [DocumentationController::class, 'show']);