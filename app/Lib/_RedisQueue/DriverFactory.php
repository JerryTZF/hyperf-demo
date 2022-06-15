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
namespace App\Lib\_RedisQueue;

use Hyperf\AsyncQueue\Driver\DriverFactory as HyperfDriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;
use Hyperf\Utils\ApplicationContext;

class DriverFactory
{
    /**
     * 获取指定队列实例.
     */
    public static function getDriverInstance(string $queueName): DriverInterface
    {
        return ApplicationContext::getContainer()->get(HyperfDriverFactory::class)->get($queueName);
    }
}
