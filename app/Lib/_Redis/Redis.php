<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Name: Redis.php
 * User: JerryTian<tzfforyou@163.com>
 * Date: 2021/6/30
 * Time: 下午3:58
 */

namespace App\Lib\_Redis;

use Hyperf\Utils\ApplicationContext;

/**
 * Class Redis
 * @package App\Lib\_Redis
 * Redis 工具类
 */
class Redis
{
    /**
     * 获取Redis实例
     * @return \Hyperf\Redis\Redis
     */
    public static function getRedisInstance(): \Hyperf\Redis\Redis
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Redis\Redis::class);
    }
}