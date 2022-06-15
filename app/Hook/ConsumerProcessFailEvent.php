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
namespace App\Hook;

use Throwable;

// 自定义进程退出事件
class ConsumerProcessFailEvent
{
    public Throwable $throwable;

    public string $name;

    public function __construct(Throwable $throwable, string $name)
    {
        $this->throwable = $throwable;
        $this->name = $name;
    }
}
