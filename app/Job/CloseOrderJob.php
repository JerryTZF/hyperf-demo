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

use App\Lib\_Log\Log;
use App\Model\Good;
use App\Model\SaleRecords;

class CloseOrderJob extends AbstractJob
{
    public function __construct(string $uniqueId, array $params)
    {
        parent::__construct($uniqueId, $params);
    }

    public function handle()
    {
        $orderNo = $this->params['order_no'];

        // 查询该订单是否支付, 未支付关单
        /** @var SaleRecords $orderInfo */
        $orderInfo = SaleRecords::query()
            ->where(['order_no' => $orderNo])
            ->first();
        if ($orderInfo->is_paid !== 'yes') {
            // 该商品库存加回去
            Good::query()
                ->where(['id' => $orderInfo->gid])
                ->increment('g_inventory');

            $orderInfo->is_timeout = 'yes';
            $orderInfo->save();

            Log::stdout()->warning("{$orderInfo->buyer} 未在5分钟支付, 已关闭订单");
        }
    }
}
