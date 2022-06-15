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

    // 静态调用
    public static function __callStatic($action, $args)
    {
        return self::getInstance()->{$action}(...$args);
    }

    public static function getInstance(): CacheInterface
    {
        return ApplicationContext::getContainer()->get(CacheInterface::class);
    }

    // 清除缓存
    public function flush(string $listener, array $args)
    {
        $this->dispatcher->dispatch(new DeleteListenerEvent($listener, $args));
    }
}
