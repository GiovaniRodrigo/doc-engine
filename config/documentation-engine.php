<?php

return [
    'docs_path' => env('DOC_ENGINE_PATH', 'docs'),

    'layout' => env(
        'DOC_ENGINE_LAYOUT',
        'documentation-engine::layouts.doc-engine'
    ),

    'css' => env(
        'DOC_ENGINE_CSS',
        null
    ),

];
