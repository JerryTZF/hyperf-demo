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

namespace App\Listener;

use App\Hook\ConsumerProcessFailEvent;
use App\Lib\_Log\Log;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Process\Event\AfterProcessHandle;
use Hyperf\Process\Event\BeforeProcessHandle;

#[Listener]
// 自定义进程异常退出监听器
class ConsumerProcessFailListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ConsumerProcessFailEvent::class, // 自定义异常捕获后触发该事件
            AfterProcessHandle::class, // 系统事件(进程退出时触发)
            BeforeProcessHandle::class, // 系统事件(进程创建时触发)
        ];
    }

    public function process(object $event)
    {
        switch (true) {
            case $event instanceof ConsumerProcessFailEvent:
                [$msg, $level] = [
                    sprintf('[自定义进程异常监听器][进程:%s][错误:%s]', $event->name, $event->throwable->getMessage()),
                    'error'
                ];
                break;
            case $event instanceof AfterProcessHandle:
                [$msg, $level] = [
                    sprintf('[自定义进程停止][进程:%s][第 %s 个进程]', $event->process->name, $event->index),
                    'warning'
                ];
                break;
            case $event instanceof BeforeProcessHandle:
                [$msg, $level] = [
                    sprintf('[自定义进程启动][进程:%s][第 %s 个进程]', $event->process->name, $event->index),
                    'info'
                ];
                break;
            default:
                [$msg, $level] = ['', 'info'];
        }

        Log::stdout()->{$level}($msg);
    }
}
