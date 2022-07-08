<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

$config = [];
foreach ([1, 2] as $id) {
    $config['default_' . $id] = [
        'driver' => env('DB_DRIVER', 'mysql'),
        'host' => env('DB_HOST', 'localhost'),
        'port' => env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'hyperf') . '_' . $id,
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => env('DB_CHARSET', 'utf8mb4'),
        'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
        'prefix' => env('DB_PREFIX', ''),
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 32,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) env('DB_MAX_IDLE_TIME', 60),
        ],
        'commands' => [
            'gen:model' => [
                'path' => 'app/Model',
                'force_casts' => true,
                'inheritance' => 'Model',
            ],
        ],
    ];
}

return $config;

//return [
//    'default' => [
//        'driver' => env('DB_DRIVER', 'mysql'),
//        'host' => env('DB_HOST', 'localhost'),
//        'database' => env('DB_DATABASE', 'hyperf'),
//        'port' => env('DB_PORT', 3306),
//        'username' => env('DB_USERNAME', 'root'),
//        'password' => env('DB_PASSWORD', ''),
//        'charset' => env('DB_CHARSET', 'utf8'),
//        'collation' => env('DB_COLLATION', 'utf8_unicode_ci'),
//        'prefix' => env('DB_PREFIX', ''),
//        'pool' => [
//            'min_connections' => 1,
//            'max_connections' => 30,
//            'connect_timeout' => 10.0,
//            'wait_timeout' => 3.0,
//            'heartbeat' => -1,
//            'max_idle_time' => (float)env('DB_MAX_IDLE_TIME', 60),
//        ],
//        'commands' => [
//            'gen:model' => [
//                'path' => 'app/Model',
//                'force_casts' => true,
//                'inheritance' => 'Model',
//            ],
//        ],
//    ],
//];
