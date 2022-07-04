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
return [
    // 自定义队列进程的队列名称
    'redis-queue' => [
        // 使用驱动(这里我们使用Redis作为驱动。AMQP等其他自行更换)
        //        'driver' => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        'driver' => App\Process\AsyncQueue\Driver::class,
        // Redis连接信息
        'redis' => [
            'pool' => 'default',
        ],
        // 队列前缀
        'channel' => 'queue',
        // pop 消息的超时时间(详见：brPop)
        'timeout' => 2,
        // 消息重试间隔(秒)
        // [注意]: 真正的重试时间为: retry_seconds + timeout = 7；实验所得
        'retry_seconds' => 5,
        // 消费消息超时时间
        'handle_timeout' => 10,
        // 消费者进程数
        'processes' => 1,
        // 并行消费消息数目
        'concurrent' => [
            'limit' => 20,
        ],
        // 当前进程处理多少消息后重启消费者进程(0||不写=>不重启)
        'max_messages' => 0,
    ],

    // 并行消费为1的特殊队列
    'limit-queue' => [
        // 使用驱动(这里我们使用Redis作为驱动。AMQP等其他自行更换)
        'driver' => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        // Redis连接信息
        'redis' => [
            'pool' => 'default',
        ],
        // 队列前缀
        'channel' => 'limit',
        // pop 消息的超时时间(详见：brPop)
        'timeout' => 2,
        // 消息重试间隔(秒)
        // [注意]: 真正的重试时间为: retry_seconds + timeout = 7；实验所得
        'retry_seconds' => 5,
        // 消费消息超时时间
        'handle_timeout' => 10,
        // 消费者进程数
        'processes' => 1,
        // 并行消费消息数目
        'concurrent' => [
            'limit' => 1,
        ],
    ],
];
