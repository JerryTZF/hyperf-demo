<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/23 15:33
 * Author: JerryTian<tzfforyou@163.com>
 * File: StopDemoJob.php
 * Desc:
 */


namespace App\Job;

use App\Lib\_Log\Log;
use App\Lib\_Redis\Redis;
use Hyperf\Utils\Coroutine;

// 自定义消费体
class StopDemoJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params = [])
    {
        parent::__construct($uniqueId, $params);
    }

    // 模拟队列消费时,中断消费进程
    // 往队列投递较多的task即可
    public function handle()
    {
        // 停止服务会有问题的场景
        Coroutine::sleep(1);
        (Redis::getRedisInstance())->incr('work_1');
        Coroutine::sleep(1);
        (Redis::getRedisInstance())->incr('work_2');
        Log::stdout()->info("第 {$this->uniqueId} 号消息被消费成功!!!");

        // 原子性消息

    }
}