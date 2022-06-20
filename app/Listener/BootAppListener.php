<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/6/16 11:13
 * Author: JerryTian<tzfforyou@163.com>
 * File: BootAppListener.php
 * Desc:
 */


namespace App\Listener;

use App\Lib\_Log\Log;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BeforeServerStart;
use Hyperf\Framework\Event\BeforeWorkerStart;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Utils\ApplicationContext;
use Swoole\Table;

#[Listener]
class BootAppListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            BootApplication::class,
            BeforeServerStart::class
        ];
    }

    public function process(object $event)
    {
        if ($event instanceof BootApplication) {
            $logo = <<<EFO

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