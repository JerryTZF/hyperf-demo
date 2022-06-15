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
namespace App\Middleware;

use App\Constants\SystemCode;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * 404自定义.
     */
    protected function handleNotFound(ServerRequestInterface $request): ResponseInterface
    {
        return $this->response()->withHeader('Content-Type', 'application/json')
            ->withStatus(404)->withBody(new SwooleStream(json_encode([
                'code' => SystemCode::ROUTE_NOT_FOUND,
                'msg' => SystemCode::getMessage(SystemCode::ROUTE_NOT_FOUND),
                'status' => false,
                'data' => [],
            ], JSON_UNESCAPED_UNICODE)));
    }

    /**
     * 405自定义.
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): ResponseInterface
    {
        return $this->response()->withHeader('Content-Type', 'application/json')
            ->withStatus(405)->withBody(new SwooleStream(json_encode([
                'code' => SystemCode::HTTP_METHOD_ERR,
                'msg' => SystemCode::getMessage(SystemCode::HTTP_METHOD_ERR),
                'status' => false,
                'data' => [],
            ], JSON_UNESCAPED_UNICODE)));
    }
}
