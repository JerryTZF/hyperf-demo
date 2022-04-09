<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/4/3 18:39
 * Author: JerryTian<tzfforyou@163.com>
 * File: LimitQueueProcess.php
 * Desc: 并行消费为1的队列(防止超卖、原子执行)
 */


namespace App\Process;

use Hyperf\AsyncQueue\Process\ConsumerProcess;
use Hyperf\Process\Annotation\Process;

#[Process(
    nums: 1,
    name: 'LimitQueueProcess',
    enableCoroutine: true,
    redirectStdinStdout: false
)]
class LimitQueueProcess extends ConsumerProcess
{
    protected $queue = 'limit-queue';
}