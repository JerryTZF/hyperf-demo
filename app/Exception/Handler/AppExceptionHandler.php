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
use App\Lib\_Log\Log;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        // CLI异常输出
        Log::stdout()->error($throwable->getMessage(), $throwable->getTrace());

        // DISK异常记录
        $errorInfo = sprintf('发生系统异常:%s;行号为:[%s];文件为:[%s]', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
        Log::get('AppExceptionHandler@handle')->error($errorInfo);

        return $response->withHeader('Content-Type', 'application/json')
            ->withStatus(500)
            ->withBody(new SwooleStream(json_encode([
                'code'   => SystemCode::SYSTEM_ERROR,
                'msg'    => SystemCode::getMessage(SystemCode::SYSTEM_ERROR),
                'status' => false,
                'data'   => []
            ], JSON_UNESCAPED_UNICODE)));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
