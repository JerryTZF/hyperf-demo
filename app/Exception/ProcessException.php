<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 14:26
 * Author: JerryTian<tzfforyou@163.com>
 * File: ProcessException.php
 * Desc:
 */


namespace App\Exception;

use Hyperf\Server\Exception\ServerException;
use Throwable;

class ProcessException extends ServerException
{
    public function __construct(int $code = 0, string $message = null, Throwable $previous = null)
    {
        if (is_null($message)) {
            $message = '当前进程发生异常';
        }

        parent::__construct($message, $code, $previous);
    }
}