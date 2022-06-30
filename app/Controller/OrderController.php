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
namespace App\Controller;

use App\Constants\ErrorCode;
use App\Exception\BusinessException;
use App\Job\CloseOrderJob;
use App\Lib\_Lock\RedisLock;
use App\Lib\_RedisQueue\DriverFactory;
use App\Model\Good;
use App\Model\SaleRecords;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'order')]
class OrderController extends AbstractController
{
    #[GetMapping(path: 'create_order')]
    public function creatOrder(): array
    {
        $buyer = $this->request->input('buyer');

        $uniqueRequest = uniqid();
        $isGetLock = RedisLock::muxLock(uniqueID: $uniqueRequest, key: $buyer);
        if (! $isGetLock) {
            throw new BusinessException(ErrorCode::GET_LOCK_ERR);
        }
        defer(fn () => RedisLock::muxUnlock($uniqueRequest, $buyer));

        // 创建订单
        /** @var Good $dove */
        $dove = Good::query()->where(['g_name' => '德芙巧克力(200g)'])->first();

        if ($dove->g_inventory <= 0) {
            throw new BusinessException(ErrorCode::INVENTORY_ERR);
        }

        $orderNo = date('YmdHis') . uniqid();
        $driver = DriverFactory::getDriverInstance('redis-queue');
        // 创建待支付订单
        (new SaleRecords([
            'gid' => $dove->id,
            'order_no' => $orderNo,
            'buyer' => $buyer,
            'amount' => $dove->g_price,
        ]))->save();

        // 优先扣减库存, 未支付或支付失败加回去
        --$dove->g_inventory;
        $dove->save();

        // 投递该订单的超时消息
        $message = new CloseOrderJob($uniqueRequest, ['order_no' => $orderNo]);
        $driver->push($message, 300);

        return $this->result->getResult();
    }
}
