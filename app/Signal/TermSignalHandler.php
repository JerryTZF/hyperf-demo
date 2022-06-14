<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/6/14 18:15
 * Author: JerryTian<tzfforyou@163.com>
 * File: TermSignalHandler.php
 * Desc:
 */


namespace App\Signal;

use Hyperf\Process\ProcessManager;
use Hyperf\Signal\Annotation\Signal;
use Hyperf\Signal\SignalHandlerInterface;

#[Signal]
class TermSignalHandler implements SignalHandlerInterface
{

    public function listen(): array
    {
        return [
            [SignalHandlerInterface::PROCESS, SIGTERM]
        ];
    }

    public function handle(int $signal): void
    {
        ProcessManager::setRunning(false);
    }
}