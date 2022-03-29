<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/29 11:32
 * Author: JerryTian<tzfforyou@163.com>
 * File: LoginStatusDemoController.php
 * Desc:
 */


namespace App\Controller;

use App\Constants\ErrorCode;
use App\Constants\StaticCode;
use App\Middleware\CheckCookieMiddleware;
use App\Model\Admin;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

// 登录态相关演示
#[Controller(prefix: "login")]
class LoginStatusDemoController extends AbstractController
{
    #[PostMapping(path: "set_cookie")]
    public function setCookie(): array|ResponseInterface
    {
        $account = $this->request->input('account');
        $password = $this->request->input('password');

        /** @var Admin $adminInfo */
        $adminInfo = Admin::query()->where(['account' => $account, 'pwd' => md5($password)])->first();
        if (!is_null($adminInfo)) {
            $cookie = new Cookie(StaticCode::LOGIN_COOKIE_NAME, $adminInfo->account);
            return $this->response->withCookie($cookie)->json($this->result->getResult());
        }

        [$e, $m] = [ErrorCode::ADMIN_404, ErrorCode::getMessage(ErrorCode::ADMIN_404)];
        return $this->result->setErrorInfo($e, $m)->getResult();
    }

    #[PostMapping(path: "check_cookie")]
    #[Middleware(CheckCookieMiddleware::class)]
    public function checkCookie(): array
    {
        /** @var Admin $admin */
        $admin = $this->request->getAttribute('admin');
        return $admin->toArray();
    }
}