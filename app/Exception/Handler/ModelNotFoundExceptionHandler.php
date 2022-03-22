<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 13:50
 * Author: JerryTian<tzfforyou@163.com>
 * File: ModelNotFoundExceptionHandler.php
 * Desc:
 */

namespace App\Exception\Handler;

use App\Constants\SystemCode;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ModelNotFoundExceptionHandler extends ExceptionHandler
{

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // 禁止异常冒泡
        $this->stopPropagation();

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200)->withBody(new SwooleStream(json_encode([
                'code'   => SystemCode::DATA_NOT_FOUND,
                'msg'    => SystemCode::getMessage(SystemCode::DATA_NOT_FOUND),
                'status' => false,
                'data'   => []
            ], JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ModelNotFoundException;
    }
}