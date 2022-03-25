<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/25 11:35
 * Author: JerryTian<tzfforyou@163.com>
 * File: DemoService.php
 * Desc:
 */


namespace App\Service;

use App\Lib\_Cache\Cache;
use App\Lib\_Result\Result;
use App\Model\Admin;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Di\Annotation\Inject;

class DemoService
{
    #[Inject]
    protected Result $result;

    // 这里生成缓存的KEY是: "c:admin:Jerry_12345678"
    #[Cacheable(prefix: "admin", ttl: 300, value: "#{account}_#{uuid}", listener: "UPDATE-ADMIN-INFO")]
    public function getAdminInfo(string $account, string $uuid = '12345678'): array
    {
        $adminInfo = Admin::query()->where(['account' => $account])->firstOrFail();
        return $adminInfo->toArray();
    }

    public function updateAdminInfo(string $account, string $password): array
    {
        /** @var Admin $adminInfo */
        $adminInfo = Admin::query()->where(['account' => $account])->firstOrFail();
        $adminInfo->pwd = $password; // 模型中有修改器，所以无需加密
        $adminInfo->save();

        // 刷新缓存
        (new Cache())->flush('UPDATE-ADMIN-INFO', [
            'account' => $account,
            'uuid'    => '12345678'
        ]);

        return $this->result->getResult();
    }
}