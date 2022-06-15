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
namespace App\Job;

use App\Model\Good;

// 自定义消费体
class StopDemoJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params = [])
    {
        parent::__construct($uniqueId, $params);
    }

    public function handle()
    {
        Good::query()->where(['g_name' => '德芙巧克力(200g)'])->increment('g_inventory');
    }
}
