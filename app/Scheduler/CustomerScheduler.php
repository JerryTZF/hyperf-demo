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
namespace App\Scheduler;

use App\Exception\SchedulerException;
use Hyperf\Crontab\Annotation\Crontab;

#[Crontab(
    name: 'CustomerScheduler',
    rule: '\/10 * * * * *',
    callback: 'execute',
    memo: '测试定时任务',
    enable: 'isEnable'
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
        return env('APP_ENV') === 'pro';
    }
}
