<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 16:19
 * Author: JerryTian<tzfforyou@163.com>
 * File: AsyncQueueProcess.php
 * Desc:
 */


namespace App\Process;

use Hyperf\Process\Annotation\Process;

#[Process(
    nums: 1,
    name: 'AsyncQueueProcess',
    enableCoroutine: true,
    redirectStdinStdout: false
)]
class AsyncQueueProcess extends \Hyperf\AsyncQueue\Process\ConsumerProcess
{
    protected $queue = 'redis-queue';
}