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
namespace App\Job;

// 模拟消费失败的任务
use App\Exception\AsyncQueueException;
use App\Lib\_Log\Log;
use Carbon\Carbon;
use Hyperf\Utils\Coroutine;

// 自定义异常消费体
class ErrorDemoJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params = [])
    {
        parent::__construct($uniqueId, $params);
    }

    // 可以测试重试次数、重试时间间隔
    // 会被 AsyncRedisQueueListener 监听器监听到
    public function handle()
    {
        Coroutine::sleep(1);
        Log::stdout()->warning(Carbon::now()->toTimeString());
        throw new AsyncQueueException(2131, "任务ID={$this->uniqueId} 消费失败");
    }
}
