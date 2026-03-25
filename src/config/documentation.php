<?php

return [

    'storage_path' => storage_path('docs'),

    'route_prefix' => 'docs',

    'repository' => 'file', // file | eloquent | git

    'cache_navigation' => true,

    'tenant_resolver' => fn () => null,

];