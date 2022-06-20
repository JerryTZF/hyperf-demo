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
return [
    // 框架提供了 error_reporting() 错误级别的监听器
    Hyperf\ExceptionHandler\Listener\ErrorExceptionHandler::class,
    // 自动将 timeout 队列中消息移动到 waiting 队列中，等待下次消费(请确保任务的幂等性)
    //    Hyperf\AsyncQueue\Listener\ReloadChannelListener::class
];
