<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 15:08
 * Author: JerryTian<tzfforyou@163.com>
 * File: FailToExecuteCrontabListener.php
 * Desc:
 */


namespace App\Listener;

use App\Lib\_Log\Log;
use Hyperf\Crontab\Event\FailToExecute;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
// 自定义定时任务异常监听器(底层已经触发,无需手动触发)
class FailToExecuteCrontabListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            FailToExecute::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof FailToExecute) {
            $info = sprintf('[定时任务异常监听器][任务:%s][错误:%s]', $event->crontab->getName(), $event->throwable->getMessage());
            Log::stdout()->error($info);
        } else {
            var_dump($event);
        }
    }
}