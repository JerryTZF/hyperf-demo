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
namespace App\Listener;

use App\Lib\_Log\Log;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeServerStart;
use Hyperf\Framework\Event\BootApplication;
use Swoole\Table;

#[Listener]
class BootAppListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
            BeforeServerStart::class,
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof BootApplication) {
            $logo = <<<'EFO'

    __        __                                       
   / /_  ____/ /_  __   ________  ______   _____  _____
  / __ \/ __  / / / /  / ___/ _ \/ ___/ | / / _ \/ ___/
 / /_/ / /_/ / /_/ /  (__  )  __/ /   | |/ /  __/ /    
/_.___/\__,_/\__, /  /____/\___/_/    |___/\___/_/     
            /____/                                     

EFO;

            Log::stdout()->info($logo);
        }

        // 初始化 `高性能共享内存Table`
        if ($event instanceof BootApplication) {
            $table = new Table(1024);
            $table->column('isKillAsyncMQ', Table::TYPE_STRING, 64);
            $table->create();

            // TODO 如何传递给全局server?
        }
    }
}
