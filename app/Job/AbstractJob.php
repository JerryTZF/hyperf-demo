<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/22 16:27
 * Author: JerryTian<tzfforyou@163.com>
 * File: AbstractJob.php
 * Desc:
 */


namespace App\Job;

use Hyperf\AsyncQueue\Job;

abstract class AbstractJob extends Job
{
    /**
     * 最大尝试次数(max = $maxAttempts+1)
     * @var int
     */
    public $maxAttempts = 2;

    /**
     * 任务编号(传递编号相同任务会被覆盖!)
     * @var string
     */
    public string $uniqueId;

    /**
     * 消息参数
     * @var array
     */
    public array $params;

    public function __construct(string $uniqueId, array $params)
    {
        [$this->uniqueId, $this->params] = [$uniqueId, $params];
    }

    public function handle()
    {
    }
}