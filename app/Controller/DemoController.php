<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/25 11:22
 * Author: JerryTian<tzfforyou@163.com>
 * File: DemoController.php
 * Desc:
 */


namespace App\Controller;

use App\Constants\CacheKeys;
use App\Lib\_Cache\Cache;
use App\Middleware\CheckTokenMiddleware;
use App\Service\DemoService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\Arr;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "demo")]
class DemoController extends AbstractController
{
    #[Inject]
    protected DemoService $service;

    // 演示异地登录 + 缓存
    #[PostMapping(path: "get_admin_info")]
    #[Middleware(CheckTokenMiddleware::class)]
    public function getAdminInfo(): array
    {
        $account = $this->request->input('account');
        $uuid = $this->request->input('uuid');
        return $this->service->getAdminInfo($account, $uuid);
    }

    // 演示更新数据刷新缓存
    #[PostMapping(path: "update_admin_info")]
    #[Middleware(CheckTokenMiddleware::class)]
    public function updateAdminInfo(): array
    {
        $account = $this->request->input('account');
        $password = $this->request->input('password');
        return $this->service->updateAdminInfo($account, $password);
    }

    #[PostMapping(path: "cache")]
    public function simpleCache(): array
    {
        $cache = Cache::getInstance();

        // 一般对于缓存,Key里面会加入一些变量,那么可以将变量写入枚举类
        $key = sprintf(CacheKeys::IS_USER_LOGON, 'YOUR_APPID', 'USER_ID');
        // 一次写入单个缓存
        $cache->set($key, ['a' => 'b'], 300);
        // 读取单个缓存
        $cacheData = $cache->get($key, '');
        // 一次写入多个缓存(具有原子性)
        $cache->setMultiple(['key1' => 'value1', 'key2' => 'value2'], 300);
        // 一次读取多个缓存
        $multipleData = $cache->getMultiple(['key1', 'key2'], []);

        // 清除所有的key
        $cache->clear();

        return $this->result->setData([
            'single'   => $cacheData,
            'multiple' => $multipleData
        ])->getResult();
    }

    // 重定向演示
    #[GetMapping(path: "redirect_2_wiki")]
    public function redirect(): ResponseInterface
    {
        return $this->response->redirect('https://wiki.tzf-foryou.xyz');
    }
}