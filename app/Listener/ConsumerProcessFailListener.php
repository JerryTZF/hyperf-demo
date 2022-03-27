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
use Hyperf\Process\Event\AfterProcessHandle;

#[Listener]
// 自定义进程异常退出监听器
class ConsumerProcessFailListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ConsumerProcessFailEvent::class, // 自定义异常捕获后触发该事件
            AfterProcessHandle::class // 系统事件(进程退出时触发)
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof ConsumerProcessFailEvent) {
            $info = sprintf('[自定义进程异常监听器][进程:%s][错误:%s]', $event->name, $event->throwable->getMessage());
            Log::stdout()->error($info);
        } elseif ($event instanceof AfterProcessHandle) {
            $info = sprintf('[自定义进程停止][进程:%s][第 %s 个进程]', $event->process->name, $event->index);
            Log::stdout()->warning($info);
        } else {
            var_dump($event);
        }
    }
}