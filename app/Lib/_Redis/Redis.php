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
namespace App\Lib\_Redis;

use Hyperf\Utils\ApplicationContext;

/**
 * Class Redis.
 */
class Redis
{
    /**
     * 获取Redis实例.
     */
    public static function getRedisInstance(): \Hyperf\Redis\Redis
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Redis\Redis::class);
    }
}
