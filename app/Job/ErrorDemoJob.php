<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 17:42
 * Author: JerryTian<tzfforyou@163.com>
 * File: ErrorDemoJob.php
 * Desc:
 */


namespace App\Job;

// 模拟消费失败的任务
use App\Exception\AsyncQueueException;

class ErrorDemoJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params = [])
    {
        parent::__construct($uniqueId, $params);
    }

    public function handle()
    {
        if ($this->uniqueId === '155') {
            throw new AsyncQueueException(2131,"任务ID={$this->uniqueId} 消费失败");
        }
    }
}