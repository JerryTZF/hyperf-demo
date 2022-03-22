<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 15:53
 * Author: JerryTian<tzfforyou@163.com>
 * File: SchedulerException.php
 * Desc:
 */


namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class SchedulerException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = '定时任务发生异常';
        }

        parent::__construct($message, $code, $previous);
    }
}