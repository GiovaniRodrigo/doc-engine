<?php

use Illuminate\Support\Facades\Route;
use Giovani\DocumentationEngine\Http\DocumentationController;

Route::get('/docs/{slug}', [DocumentationController::class, 'show']);
Route::post('/docs/webhooks/github', [DocumentationController::class, 'githubWebhook']);
Route::post('/docs/webhooks/gitlab', [DocumentationController::class, 'gitlabWebhook']);
