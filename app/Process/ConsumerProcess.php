<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 14:24
 * Author: JerryTian<tzfforyou@163.com>
 * File: ConsumerProcess.php
 * Desc:
 */


namespace App\Process;

use App\Exception\ProcessException;
use App\Hook\ConsumerProcessFailEvent;
use App\Job\ConsumerDemoJob;
use App\Job\ErrorDemoJob;
use App\Job\StopDemoJob;
use App\Lib\_RedisQueue\DriverFactory;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Process\ProcessManager;
use Hyperf\Utils\Coroutine;
use Psr\EventDispatcher\EventDispatcherInterface;

#[Process(
    nums: 1,
    name: 'ConsumerProcess',
    enableCoroutine: true,
    redirectStdinStdout: false
)]
class ConsumerProcess extends AbstractProcess
{
    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    // 演示投递异步消息 && 自定义进程退出
    public function handle(): void
    {
        $index = 0;
        try {
            while (ProcessManager::isRunning()) {
                $index += 1;
                Coroutine::sleep(1);
                if ($index > 300000) {
                    throw new ProcessException(500, '自定义进程异常抛出测试');
                }
                if ($index === 1) {
                    for ($i = 200; $i--;) {
                        // 向异步队列中投递消息
                        $driver = DriverFactory::getDriverInstance('redis-queue');
                        $driver->push(new StopDemoJob((string)$i, [$i]));
                    }
                }
            }
        } catch (ProcessException $e) {
            $this->dispatcher->dispatch(new ConsumerProcessFailEvent($e, 'ConsumerProcess'));
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function isEnable($server): bool
    {
        return env('APP_ENV', 'dev') === 'dev';
    }
}