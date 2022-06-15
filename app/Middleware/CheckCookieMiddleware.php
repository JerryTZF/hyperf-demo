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

use App\Constants\ErrorCode;
use App\Constants\StaticCode;
use App\Model\Admin;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as proxyResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

// 校验Cookie中间件
class CheckCookieMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected proxyResponse $response;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookie = $this->request->cookie(StaticCode::LOGIN_COOKIE_NAME);
        if ($cookie === null) {
            return $this->response->json([
                'code' => ErrorCode::COOKIE_NOT_FOUND,
                'msg' => ErrorCode::getMessage(ErrorCode::COOKIE_NOT_FOUND),
                'status' => false,
                'data' => [],
            ]);
        }

        $admin = Admin::query()->where(['account' => $cookie])->first();
        if ($admin === null) {
            return $this->response->json([
                'code' => ErrorCode::COOKIE_NOT_FOUND,
                'msg' => ErrorCode::getMessage(ErrorCode::COOKIE_NOT_FOUND),
                'status' => false,
                'data' => [],
            ]);
        }
        $request = Context::set(ServerRequestInterface::class, $request->withAttribute('admin', $admin));
        return $handler->handle($request);
    }
}
