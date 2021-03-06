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

// 该任务只能原子执行示例
use App\Lib\_Log\Log;
use App\Model\Good;
use App\Model\SaleRecords;

class OversoldJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params)
    {
        parent::__construct($uniqueId, $params);
    }

    public function handle(): void
    {
        /** @var Good $dove */
        $dove = Good::query()->where(['g_name' => '德芙巧克力(200g)'])->first();
        if ($dove->g_inventory > 0) {
            (new SaleRecords([
                'gid' => $dove->id,
                'order_no' => date('YmdHis') . uniqid(),
                'buyer' => $this->uniqueId,
                'amount' => $dove->g_price,
            ]))->save();

            --$dove->g_inventory;
            $dove->save();
            return;
        }

        Log::stdout()->warning('德芙巧克力(200g) 库存不足!!!');
    }
}
