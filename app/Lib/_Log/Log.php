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
namespace App\Lib\_Log;

use Carbon\Carbon;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;

/**
 * @method static void info(string $msg, array $content = [])
 * @method static void warning(string $msg, array $content = [])
 * @method static void error(string $msg, array $content = [])
 * @method static void alert(string $msg, array $content = [])
 * @method static void critical(string $msg, array $content = [])
 * @method static void emergency(string $msg, array $content = [])
 * @method static void notice(string $msg, array $content = [])
 */
class Log
{
    public static function __callStatic(string $level, array $args = []): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $trace = array_pop($trace);

        [$now, $caller, $message] = [
            Carbon::now()->toDateTimeString(),
            "{$trace['class']}@{$trace['function']}",
            $args[0],
        ];

        $msg = "[time: {$now}]|[caller: {$caller}]|[message: {$message}]";

        // CLI输出(没有$content)
        static::stdout()->{$level}($msg);
        // DISK输出
        static::get($caller)->{$level}($msg, $args[1] ?? []);
    }

    /**
     * 获取Logger实例.
     */
    public static function get(string $channel = ''): LoggerInterface
    {
        return ApplicationContext::getContainer()->get(LoggerFactory::class)->get($channel);
    }

    /**
     * CLI 日志实例.
     */
    public static function stdout(): StdoutLoggerInterface
    {
        return ApplicationContext::getContainer()->get(StdoutLoggerInterface::class);
    }
}
