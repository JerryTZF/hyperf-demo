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
        $errorMsg = $throwable->validator->errors()->first();

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(422)->withBody(new SwooleStream(json_encode([
                'code' => SystemCode::VALIDATOR_ERR,
                'msg' => SystemCode::getMessage(SystemCode::VALIDATOR_ERR, $errorMsg),
                'status' => false,
                'data' => [],
            ], JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
