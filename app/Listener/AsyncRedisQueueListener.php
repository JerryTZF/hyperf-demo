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
use Hyperf\AsyncQueue\AnnotationJob;
use Hyperf\AsyncQueue\Event\AfterHandle;
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
            // 消费队列事件
            AfterHandle::class,
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

    public function process(object $event): void
    {
        switch (true) {
            case $event instanceof AfterHandle:
                $job = $event->message->job();
                $jobClass = get_class($job);
                if ($job instanceof AnnotationJob) {
                    $jobClass = sprintf('Job[%s@%s]', $job->class, $job->method);
                }
                $message = sprintf('[%s] Processed %s.', date('Y-m-d H:i:s'), $jobClass);
                Log::info($message);
                break;
            case $event instanceof QueueLength:
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
            case $event instanceof FailedHandle:
                [$msg, $trace] = ['消息最终消费失败,原因为:' . $event->getThrowable()->getMessage(), $event->getThrowable()->getTrace()];
                Log::error($msg, $trace);
                break;
            case $event instanceof RetryHandle:
                [$msg, $trace] = ['消息正在重试,原因为:' . $event->getThrowable()->getMessage(), $event->getThrowable()->getTrace()];
                Log::warning($msg, $trace);
                break;
            default:
                Log::warning('未知事件!!!');
        }
    }
}
