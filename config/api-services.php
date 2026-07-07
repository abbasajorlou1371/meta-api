<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API docs source directory
    |--------------------------------------------------------------------------
    |
    | Markdown files under this path define which routes belong to each
    | microservice. Folder names map to documentation tags (e.g. auth-service).
    |
    */
    'docs_path' => base_path('api-docs'),

    /*
    |--------------------------------------------------------------------------
    | Display names & sidebar ordering
    |--------------------------------------------------------------------------
    */
    'tags' => [
        'auth-service' => ['name' => 'Auth Service', 'weight' => 10],
        'calendar-service' => ['name' => 'Calendar Service', 'weight' => 20],
        'dynasty-service' => ['name' => 'Dynasty Service', 'weight' => 30],
        'features-service' => ['name' => 'Features Service', 'weight' => 40],
        'finantial-service' => ['name' => 'Financial Service', 'weight' => 50],
        'levels-service' => ['name' => 'Levels Service', 'weight' => 60],
        'notification-service' => ['name' => 'Notification Service', 'weight' => 70],
        'search' => ['name' => 'Search Service', 'weight' => 80],
        'social-service' => ['name' => 'Social Service', 'weight' => 90],
        'storage-service' => ['name' => 'Storage Service', 'weight' => 100],
        'support-service' => ['name' => 'Support Service', 'weight' => 110],
        'training-service' => ['name' => 'Training Service', 'weight' => 120],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route overrides
    |--------------------------------------------------------------------------
    |
    | Applied before api-docs rules (most specific first). Use for live routes
    | that differ from markdown paths or split a shared prefix across services.
    |
    */
    'route_overrides' => [
        ['pattern' => '#^api/citizen/[^/]+/wallet/history#', 'service' => 'finantial-service'],
        ['pattern' => '#^api/citizen/[^/]+/features#', 'service' => 'features-service'],
        ['pattern' => '#^api/citizen/[^/]+/buildings#', 'service' => 'features-service'],
        ['pattern' => '#^api/user/#', 'service' => 'finantial-service'],
        ['pattern' => '#^api/wallet/#', 'service' => 'auth-service'],
        ['pattern' => '#^api/reset/#', 'service' => 'auth-service'],
        ['pattern' => '#^api/order#', 'service' => 'finantial-service'],
        ['pattern' => '#^api/maps#', 'service' => 'features-service'],
        ['pattern' => '#^api/broadcasting/auth#', 'service' => 'auth-service'],
    ],
];
