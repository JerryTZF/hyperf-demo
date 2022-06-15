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
namespace App\Process;

use App\Exception\ProcessException;
use App\Hook\ConsumerProcessFailEvent;
use App\Job\ConsumerDemoJob;
use App\Lib\_RedisQueue\DriverFactory;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Process\ProcessManager;
use Hyperf\Utils\Coroutine;
use Psr\EventDispatcher\EventDispatcherInterface;

// #[Process(
//    nums: 1,
//    name: 'ConsumerQueueProcess',
//    enableCoroutine: true,
//    redirectStdinStdout: false
// )]
class ConsumerQueueProcess extends AbstractProcess
{
    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    // 演示投递异步消息 && 自定义进程退出
    public function handle(): void
    {
        $index = 0;
        try {
            while (ProcessManager::isRunning()) {
                ++$index;
                Coroutine::sleep(1);
                if ($index > 300000) {
                    throw new ProcessException(500, '自定义进程异常抛出测试');
                }
                if ($index === -1) {
                    $driver = DriverFactory::getDriverInstance('redis-queue');
                    for ($i = 2000; --$i;) {
                        // 向异步队列中投递消息
                        $driver->push(new ConsumerDemoJob((string) $i, [$i]));
                    }
                }
            }
        } catch (ProcessException $e) {
            $this->dispatcher->dispatch(new ConsumerProcessFailEvent($e, 'ConsumerQueueProcess'));
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function isEnable($server): bool
    {
        return env('APP_ENV', 'dev') === 'dev';
    }
}
