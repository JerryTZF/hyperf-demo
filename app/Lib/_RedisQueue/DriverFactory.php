<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 16:25
 * Author: JerryTian<tzfforyou@163.com>
 * File: DriverFactory.php
 * Desc:
 */


namespace App\Lib\_RedisQueue;

use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Utils\ApplicationContext;
use Hyperf\AsyncQueue\Driver\DriverFactory as HyperfDriverFactory;

class DriverFactory
{
    /**
     * 获取指定队列实例
     * @param string $queueName
     * @return DriverInterface
     */
    public static function getDriverInstance(string $queueName): DriverInterface
    {
        return ApplicationContext::getContainer()->get(HyperfDriverFactory::class)->get($queueName);
    }
}