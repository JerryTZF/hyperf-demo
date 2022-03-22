<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Name: Log.php
 * User: JerryTian<tzfforyou@163.com>
 * Date: 2021/6/30
 * Time: 下午2:42
 */

namespace App\Lib\_Log;


use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;

/**
 * Class Log
 * @package App\Lib\_Log
 * Log相关工具
 */
class Log
{
    /**
     * 获取Logger实例
     * @param string $channel
     * @return LoggerInterface
     */
    public static function get(string $channel = ''): LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($channel);
    }

    /**
     * CLI 日志实例
     * @return StdoutLoggerInterface
     */
    public static function stdout(): StdoutLoggerInterface
    {
        return ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
    }
}