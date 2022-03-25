<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/25 11:24
 * Author: JerryTian<tzfforyou@163.com>
 * File: Cache.php
 * Desc:
 */


namespace App\Lib\_Cache;

use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\CacheInterface;

class Cache
{
    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    public static function getInstance(): CacheInterface
    {
        return ApplicationContext::getContainer()->get(CacheInterface::class);
    }

    // 静态调用
    public static function __callStatic($action, $args)
    {
        return self::getInstance()->$action(...$args);
    }

    // 清除缓存
    public function flush(string $listener, array $args)
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent($listener, $args));
    }
}