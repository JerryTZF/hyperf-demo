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
namespace App\Process\AsyncQueue;

use App\Lib\_Redis\Redis;
use Hyperf\AsyncQueue\Driver\RedisDriver;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Process\ProcessManager;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Coroutine\Concurrent;

class Driver extends RedisDriver
{
    public function consume(): void
    {
        $messageCount = 0;
        $maxMessages = Arr::get($this->config, 'max_messages', 0);

        while (ProcessManager::isRunning()) {
            try {
                if (Redis::getRedisInstance()->get('Consumer') === 'OFF') {
                    break;
                }

                [$data, $message] = $this->pop();

                if ($data === false) {
                    continue;
                }

                $callback = $this->getCallback($data, $message);

                if ($this->concurrent instanceof Concurrent) {
                    $this->concurrent->create($callback);
                } else {
                    parallel([$callback]);
                }

                if ($messageCount % $this->lengthCheckCount === 0) {
                    $this->checkQueueLength();
                }

                if ($maxMessages > 0 && $messageCount >= $maxMessages) {
                    break;
                }
            } catch (\Throwable $exception) {
                $logger = $this->container->get(StdoutLoggerInterface::class);
                $logger->error((string) $exception);
            } finally {
                ++$messageCount;
            }
        }
    }
}
