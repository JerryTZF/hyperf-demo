<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 17:44
 * Author: JerryTian<tzfforyou@163.com>
 * File: AsyncQueueException.php
 * Desc:
 */


namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class AsyncQueueException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = '当前队列消费失败';
        }

        parent::__construct($message, $code, $previous);
    }
}