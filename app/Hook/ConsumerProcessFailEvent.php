<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 15:20
 * Author: JerryTian<tzfforyou@163.com>
 * File: ConsumerProcessFailEvent.php
 * Desc:
 */


namespace App\Hook;

use Throwable;

// 自定义进程退出事件
class ConsumerProcessFailEvent
{
    public Throwable $throwable;

    public string $name;

    public function __construct(Throwable $throwable,string $name)
    {
        $this->throwable = $throwable;
        $this->name = $name;
    }
}