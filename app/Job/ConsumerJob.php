<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 16:31
 * Author: JerryTian<tzfforyou@163.com>
 * File: ConsumerJob.php
 * Desc:
 */


namespace App\Job;

use App\Lib\_Log\Log;
use Hyperf\Utils\Coroutine;

// 自定义消息体
class ConsumerJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params)
    {
        parent::__construct($uniqueId, $params);
    }

    public function handle()
    {
        // 模拟任务耗时3秒
        Coroutine::sleep(1);
        Log::stdout()->info("任务ID:{$this->uniqueId}--开始");
        Coroutine::sleep(1);
        Log::stdout()->info("任务ID:{$this->uniqueId}--进行中");
        Coroutine::sleep(1);
        Log::stdout()->info("任务ID:{$this->uniqueId}--结束");
    }
}