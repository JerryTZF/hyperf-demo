<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 14:57
 * Author: JerryTian<tzfforyou@163.com>
 * File: CustomerScheduler.php
 * Desc:
 */

namespace App\Scheduler;

use App\Exception\SchedulerException;
use Hyperf\Crontab\Annotation\Crontab;

#[Crontab(
    name: 'CustomerScheduler',
    rule: '\/10 * * * * *',
    callback: 'execute',
    memo: '测试定时任务',
    enable: "isEnable"
)]
class CustomerScheduler
{
    public function execute(): void
    {
        try {
            $a = 1 / 0;
        } catch (SchedulerException $e) {
            throw new SchedulerException(988, $e->getMessage());
        }

    }

    public function isEnable(): bool
    {
        return env('APP_ENV') === 'dev';
    }
}