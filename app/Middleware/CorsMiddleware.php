<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 11:50
 * Author: JerryTian<tzfforyou@163.com>
 * File: CorsMiddleware.php
 * Desc:
 */


namespace App\Middleware;


use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 跨域中间件设置
 * Class CorsMiddleware
 * @package App\Middleware
 */
class CorsMiddleware implements MiddlewareInterface
{
    /**
     * 跨域处理
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = Context::get(ResponseInterface::class);
        $response = $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credential', 'true')
            ->withHeader('Access-Control-Allow-Headers', 'DNT,Keep-Alive,User-Agent,Cache-Control,Content-Type,Authorization');
        Context::set(ResponseInterface::class, $response);

        if ($request->getMethod() == 'OPTIONS') {
            return $response;
        }

        return $handler->handle($request);
    }
}