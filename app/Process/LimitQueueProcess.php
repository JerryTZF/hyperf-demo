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
