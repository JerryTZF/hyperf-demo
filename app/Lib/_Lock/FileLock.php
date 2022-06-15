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
namespace App\Lib\_Lock;

use Hyperf\Utils\Coroutine;

class FileLock
{
    public static function muxLock(int $ttl = 5, float $timeout = 2.5): bool
    {
        $file = BASE_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . 'lock.txt';
        $fp = fopen($file, 'a+');
        $time = 0;
        while (true) {
            if (flock($fp, LOCK_EX)) {
                // TODO 这里不对,应该使用AOP切面,切入要锁住的上下文才可以 :(
                flock($fp, LOCK_UN);
                fclose($fp);
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
}
