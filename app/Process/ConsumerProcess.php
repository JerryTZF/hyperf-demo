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
use App\Lib\_Log\Log;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Process\ProcessManager;
use Hyperf\Utils\ApplicationContext;
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

    public function handle(): void
    {
        $index = 0;
        try {
            while (ProcessManager::isRunning()) {
                $index += 1;
                Coroutine::sleep(1);
                if ($index > 10) {
                    throw new ProcessException(500, '自定义进程异常抛出测试');
                }
            }
        } catch (ProcessException $e) {
            $this->dispatcher->dispatch(new ConsumerProcessFailEvent($e, 'ConsumerProcess'));
        }

    }

    public function isEnable($server): bool
    {
        return env('APP_ENV', 'dev') === 'dev';
    }
}