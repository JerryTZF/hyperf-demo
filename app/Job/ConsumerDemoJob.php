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

use App\Lib\_Log\Log;
use Hyperf\Utils\Coroutine;

// 自定义消息体
class ConsumerDemoJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params)
    {
        parent::__construct($uniqueId, $params);
    }

    // 模拟消息体消费超时
    public function handle()
    {
        // 模拟任务耗时3秒
        // 当配置中的 handle_timeout = 3 时，可以看到我们的消息体需要执行4秒，所以该消息一定会超时，
        // 被放入timeout队列，但是看控制台可以看到开始、进行中、结束，所以：超时不一定是失败！！！
        Coroutine::sleep(1);
        Log::stdout()->info("任务ID:{$this->uniqueId}--开始");
        Coroutine::sleep(1);
        Log::stdout()->info("任务ID:{$this->uniqueId}--进行中");
        Coroutine::sleep(1);
        Log::stdout()->info("任务ID:{$this->uniqueId}--结束");
    }
}
