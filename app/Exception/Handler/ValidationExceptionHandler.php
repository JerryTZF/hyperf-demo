<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 13:47
 * Author: JerryTian<tzfforyou@163.com>
 * File: ValidationExceptionHandler.php
 * Desc:
 */


namespace App\Exception\Handler;

use App\Constants\SystemCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // 禁止后续异常管理类接管
        $this->stopPropagation();

        /** @var ValidationException $throwable */
        $httpBody = $throwable->validator->errors()->first();

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(200)->withBody(new SwooleStream(json_encode([
                'code'   => SystemCode::VALIDATOR_ERR,
                'msg'    => SystemCode::getMessage(SystemCode::VALIDATOR_ERR) . $httpBody,
                'status' => false,
                'data'   => []
            ], JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}