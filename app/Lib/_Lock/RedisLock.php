<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/24 17:34
 * Author: JerryTian<tzfforyou@163.com>
 * File: RedisLock.php
 * Desc:
 */


namespace App\Lib\_Lock;

use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\Coroutine;

// 分布式锁
class RedisLock
{
    private static string $lockKey = 'redis_lock';

    // 尝试获取锁
    public static function muxLock(string $uniqueID, int $ttl = 5, float $timeout = 2.5, string $key = ''): bool
    {
        [$redis, $time] = [ApplicationContext::getContainer()->get(Redis::class), 0];

        if ($key === '') {
            $key = self::$lockKey;
        }

        // $ttl 请根据你的任务完成大概需要耗时来设定;

        while (true) {
            // 抢占式抢夺独占锁
            // set key clientID NX EX (原子操作)
            if ($redis->set($key, $uniqueID, ['nx', 'ex' => $ttl])) {
                return true;
            }

            if ($time > $timeout) {
                // 大量请求抢占锁时,一直未抢到锁的线程(协程)会等待时间非常长,所以需要增加超时时间处理
                // $timeout 秒内取不到锁直接放弃抢锁
                return false;
            }

            Coroutine::sleep(.25);
            $time += .25;
        }
    }

    // 释放锁
    public static function muxUnlock(string $uniqueID, string $key = ''): void
    {
        $redis = ApplicationContext::getContainer()->get(Redis::class);

        if ($redis->get($key) === $uniqueID) {
            $key === '' ? $redis->del(self::$lockKey) : $redis->del($key);
        }
    }
}