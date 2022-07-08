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

use App\Lib\_Log\Log;
use Hyperf\AsyncQueue\Event\FailedHandle;
use Hyperf\AsyncQueue\Event\QueueLength;
use Hyperf\AsyncQueue\Event\RetryHandle;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
// 自定义异步队列监听器
class AsyncRedisQueueListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            // 队列长度信息事件
            QueueLength::class,
            // 消费失败事件
            FailedHandle::class,
            // 重试消息事件
            RetryHandle::class,
        ];

        // 任务如果符合"幂等性"，那么可以开启
        // "Hyperf\AsyncQueue\Listener\ReloadChannelListener::class" 监听器
        // 作用是：自动将 timeout 队列中消息移动到 waiting 队列中，等待下次消费
    }

    public function process(object $event)
    {
        switch (get_class($event)) {
            case 'Hyperf\\AsyncQueue\\Event\\QueueLength':
                $message = sprintf('队列:%s;长度:%s', $event->key, $event->length);
                foreach (['debug' => 10, 'info' => 50, 'warning' => 500] as $lv => $value) {
                    if ($event->length < $value) {
                        Log::$lv($message);
                        break;
                    }
                }

                if ($event->length >= $value) {
                    Log::error($message);
                }
                break;
            case 'Hyperf\\AsyncQueue\\Event\\FailedHandle':
                [$msg, $trace] = ['消息最终消费失败,原因为:' . $event->getThrowable()->getMessage(), $event->getThrowable()->getTrace()];
                Log::error($msg, $trace);
                break;
            case 'Hyperf\\AsyncQueue\\Event\\RetryHandle':
                [$msg, $trace] = ['消息正在重试,原因为:' . $event->getThrowable()->getMessage(), $event->getThrowable()->getTrace()];
                Log::error($msg, $trace);
                break;
            default:
                var_dump($event);
        }
    }
}
