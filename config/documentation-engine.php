<?php

return [
    'docs_path' => env('DOC_ENGINE_PATH', 'docs'),

    'layout' => env(
        'DOC_ENGINE_LAYOUT',
        'documentation-engine::layouts.default'
    ),

    'css' => env(
        'DOC_ENGINE_CSS',
        null
    ),

    'ai' => [
        'enabled' => env('DOC_ENGINE_AI_ENABLED', true),
        'provider' => null,
    ],

];
