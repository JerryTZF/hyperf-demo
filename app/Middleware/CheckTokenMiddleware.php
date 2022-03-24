<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/24 21:54
 * Author: JerryTian<tzfforyou@163.com>
 * File: CheckTokenMiddleware.php
 * Desc:
 */


namespace App\Middleware;

use App\Constants\ErrorCode;
use App\Model\Admin;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;

// 通过中间件实现简单异地登录校验
class CheckTokenMiddleware implements MiddlewareInterface
{
    protected Response $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $token = $request->getHeaderLine('authorization');

        // 每次调用login都会重新写入token, 即:在其他设备登录后token被刷新,原token就会失效
        // 在需要登录态的API上都可以打上该注解
        $admin = Admin::where(['token' => $token])->first();
        if (null === $admin) {
            return $this->response->json([
                'code'   => ErrorCode::LONG_DISTANCE_LOGIN,
                'msg'    => ErrorCode::getMessage(ErrorCode::LONG_DISTANCE_LOGIN),
                'status' => false,
                'data'   => []
            ]);
        }
        return $handler->handle($request);
    }
}