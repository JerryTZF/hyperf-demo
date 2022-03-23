<?php

return [
    // 自定义队列进程的队列名称
    'redis-queue' => [
        // 使用驱动(这里我们使用Redis作为驱动。AMQP等其他自行更换)
        'driver'         => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        // Redis连接信息
        'redis'          => [
            'pool' => 'default'
        ],
        // 队列前缀
        'channel'        => 'queue',
        // pop 消息的超时时间(详见：brPop)
        'timeout'        => 2,
        // 消息重试间隔(秒)
        // [注意]: 真正的重试时间为: retry_seconds + timeout = 7；实验所得
        'retry_seconds'  => 5,
        // 消费消息超时时间
        'handle_timeout' => 10,
        // 消费者进程数
        'processes'      => 1,
        // 并行消费消息数目
        'concurrent'     => [
            'limit' => 20,
        ],
    ],
];