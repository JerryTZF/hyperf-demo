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
return [
    'http' => [
        // 跨域中间件
        App\Middleware\CorsMiddleware::class,
        // 验证器中间件(官方)
        Hyperf\Validation\Middleware\ValidationMiddleware::class,
    ],
];
