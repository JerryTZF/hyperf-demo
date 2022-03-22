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
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Annotation\Process;
use Hyperf\Process\ProcessManager;
use Hyperf\Utils\Coroutine;

#[Process(
    nums: 1,
    name: 'ConsumerProcess',
    enableCoroutine: true,
    redirectStdinStdout: false
)]
class ConsumerProcess extends AbstractProcess
{

    public function handle(): void
    {
        $index = 0;
        while (ProcessManager::isRunning()) {
            $index += 1;
            Coroutine::sleep(1);
            if ($index > 10) {
                throw new ProcessException(500,'index > 10');
            }
        }
    }

    public function isEnable($server): bool
    {
        return env('APP_ENV','dev') === 'dev';
    }
}