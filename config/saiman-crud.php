<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Namespace Configuration
    |--------------------------------------------------------------------------
    */
    'namespaces' => [
        'model'      => 'App\\Models',
        'controller' => 'App\\Http\\Controllers',
        'request'    => 'App\\Http\\Requests',
        'resource'   => 'App\\Http\\Resources',
        'service'    => 'App\\Services',
        'repository' => 'App\\Repositories',
        'factory'    => 'Database\\Factories',
        'seeder'     => 'Database\\Seeders',
        'livewire'   => 'App\\Livewire',
        'filament'   => 'App\\Filament\\Resources',
    ],

    /*
    |--------------------------------------------------------------------------
    | Path Configuration
    |--------------------------------------------------------------------------
    */
    'paths' => [
        'model'      => 'app/Models',
        'controller' => 'app/Http/Controllers',
        'request'    => 'app/Http/Requests',
        'resource'   => 'app/Http/Resources',
        'service'    => 'app/Services',
        'repository' => 'app/Repositories',
        'factory'    => 'database/factories',
        'seeder'     => 'database/seeders',
        'migration'  => 'database/migrations',
        'views'      => 'resources/views',
        'livewire'   => 'app/Livewire',
        'filament'   => 'app/Filament/Resources',
        'routes'     => 'routes',
    ],

    /*
    |--------------------------------------------------------------------------
    | View Configuration
    |--------------------------------------------------------------------------
    */
    'views' => [
        'layout' => 'layouts.app',
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'web_middleware' => ['web'],
        'api_middleware' => ['api'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Soft Deletes
    |--------------------------------------------------------------------------
    */
    'soft_deletes' => false,

    /*
    |--------------------------------------------------------------------------
    | Timestamps
    |--------------------------------------------------------------------------
    */
    'timestamps' => true,

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'pagination' => 15,

];
