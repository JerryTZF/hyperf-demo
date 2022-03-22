<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 11:53
 * Author: JerryTian<tzfforyou@163.com>
 * File: CoreMiddleware.php
 * Desc:
 */


namespace App\Middleware;

use App\Constants\SystemCode;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * 404自定义
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function handleNotFound(ServerRequestInterface $request): ResponseInterface
    {
        return $this->response()->withHeader('Content-Type', 'application/json')
            ->withStatus(404)->withBody(new SwooleStream(json_encode([
                'code'   => SystemCode::ROUTE_NOT_FOUND,
                'msg'    => SystemCode::getMessage(SystemCode::ROUTE_NOT_FOUND),
                'status' => false,
                'data'   => []
            ], JSON_UNESCAPED_UNICODE)));
    }

    /**
     * 405自定义
     * @param array $methods
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function handleMethodNotAllowed(array $methods, ServerRequestInterface $request): ResponseInterface
    {
        return $this->response()->withHeader('Content-Type', 'application/json')
            ->withStatus(405)->withBody(new SwooleStream(json_encode([
                'code'   => SystemCode::HTTP_METHOD_ERR,
                'msg'    => SystemCode::getMessage(SystemCode::HTTP_METHOD_ERR),
                'status' => false,
                'data'   => []
            ], JSON_UNESCAPED_UNICODE)));
    }
}