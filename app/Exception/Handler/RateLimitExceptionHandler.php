<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 13:55
 * Author: JerryTian<tzfforyou@163.com>
 * File: RateLimitExceptionHandler.php
 * Desc:
 */


namespace App\Exception\Handler;

use App\Constants\SystemCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\RateLimit\Exception\RateLimitException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RateLimitExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // 禁止异常冒泡
        $this->stopPropagation();

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200)->withBody(new SwooleStream(json_encode([
                'code'   => SystemCode::RATE_LIMIT_ERR,
                'msg'    => SystemCode::getMessage(SystemCode::RATE_LIMIT_ERR),
                'status' => false,
                'data'   => []
            ], JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof RateLimitException;
    }
}