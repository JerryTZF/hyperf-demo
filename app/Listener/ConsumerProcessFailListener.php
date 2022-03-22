<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 15:31
 * Author: JerryTian<tzfforyou@163.com>
 * File: ConsumerProcessFailListener.php
 * Desc:
 */


namespace App\Listener;

use App\Hook\ConsumerProcessFailEvent;
use App\Lib\_Log\Log;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
// 自定义进程异常退出监听器
class ConsumerProcessFailListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ConsumerProcessFailEvent::class
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof ConsumerProcessFailEvent) {
            $info = sprintf('[自定义进程异常监听器][进程:%s][错误:%s]', $event->name, $event->throwable->getMessage());
            Log::stdout()->error($info);
        } else {
            var_dump($event);
        }
    }
}