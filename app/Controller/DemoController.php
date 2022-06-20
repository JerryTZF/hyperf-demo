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

use App\Constants\CacheKeys;
use App\Constants\ErrorCode;
use App\Job\OversoldJob;
use App\Job\StopDemoJob;
use App\Lib\_Cache\Cache;
use App\Lib\_Lock\RedisLock;
use App\Lib\_Office\ExportExcelHandler;
use App\Lib\_RedisQueue\DriverFactory;
use App\Lib\_Validator\DemoValidator;
use App\Middleware\CheckTokenMiddleware;
use App\Model\Good;
use App\Model\SaleRecords;
use App\Service\DemoService;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Intervention\Image\ImageManager;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'demo')]
class DemoController extends AbstractController
{
    #[Inject]
    protected DemoService $service;

    // 演示异地登录 + 缓存
    #[PostMapping(path: 'get_admin_info')]
    #[Middleware(CheckTokenMiddleware::class)]
    public function getAdminInfo(): array
    {
        $account = $this->request->input('account');
        $uuid = $this->request->input('uuid');
        return $this->service->getAdminInfo($account, $uuid);
    }

    // 演示更新数据刷新缓存
    #[PostMapping(path: 'update_admin_info')]
    #[Middleware(CheckTokenMiddleware::class)]
    public function updateAdminInfo(): array
    {
        $account = $this->request->input('account');
        $password = $this->request->input('password');
        return $this->service->updateAdminInfo($account, $password);
    }

    #[PostMapping(path: 'cache')]
    public function simpleCache(): array
    {
        $cache = Cache::getInstance();

        // 一般对于缓存,Key里面会加入一些变量,那么可以将变量写入枚举类
        $key = sprintf(CacheKeys::IS_USER_LOGON, 'YOUR_APPID', 'USER_ID');
        // 一次写入单个缓存
        $cache->set($key, ['a' => 'b'], 300);
        // 读取单个缓存
        $cacheData = $cache->get($key, '');
        // 一次写入多个缓存(具有原子性)
        $cache->setMultiple(['key1' => 'value1', 'key2' => 'value2'], 300);
        // 一次读取多个缓存
        $multipleData = $cache->getMultiple(['key1', 'key2'], []);

        // 清除所有的key
        $cache->clear();

        return $this->result->setData([
            'single' => $cacheData,
            'multiple' => $multipleData,
        ])->getResult();
    }

    // 重定向演示
    #[GetMapping(path: 'redirect_2_wiki')]
    public function redirect(): ResponseInterface
    {
        return $this->response->redirect('https://wiki.tzf-foryou.xyz');
    }

    // 演示文件系统(阿里云OSS)
    #[PostMapping(path: 'oss')]
    public function file(FilesystemFactory $factory): array|ResponseInterface
    {
        // 获取阿里云OSS适配器
        $ossInstance = $factory->get('oss');
        $action = $this->request->input('action', 'get');

        DemoValidator::ossValidator(['action' => $action]);

        if ($action === 'get') {
            $fileName = $this->request->input('file_name');
            if ($fileName === null) {
                [$e, $m] = [ErrorCode::FILE_NAME_ERR, ErrorCode::getMessage(ErrorCode::FILE_NAME_ERR)];
                return $this->result->setErrorInfo($e, $m)->getResult();
            }
            $remotePath = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName;
            // 下载到本地
            $localPath = BASE_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . $fileName;
            file_put_contents($localPath, $ossInstance->read($remotePath));

            return $this->response->download($localPath);
        }

        if ($action === 'upload') {
            $file = $this->request->file('upload');
            [$isMimeRight, $isExtensionRight] = [
                in_array($file->getMimeType(), ['image/heic', 'image/png', 'image/jpeg']),
                in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'heic', 'PNG', 'JPG', 'JPEG', 'HEIC']),
            ];
            if (! $isMimeRight || ! $isExtensionRight) {
                [$e, $m] = [ErrorCode::FILE_MIME_ERR, ErrorCode::getMessage(ErrorCode::FILE_MIME_ERR)];
                return $this->result->setErrorInfo($e, $m)->getResult();
            }

            // 写入OSS
            $fileName = 'UPLOADER_' . date('YmdHis') . '.' . $file->getExtension();
            $remotePath = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName;
            [$endpoint, $bucket] = [env('OSS_ENDPOINT'), env('OSS_BUCKET')];
            $address = "https://{$bucket}.{$endpoint}/img/" . $fileName;
            $ossInstance->write($remotePath, file_get_contents($file->getRealPath()));
            return $this->result->setData(['address' => $address])->getResult();
        }

        return $this->result->getResult();
    }

    // 依赖 intervention/image 包 && gd或者imagick扩展
    // 详见：https://image.intervention.io/v2/usage/overview
    #[PostMapping(path: 'image')]
    public function interventionImage(): array
    {
        $file = $this->request->file('image');

        // TODO 验证图片相关...

        $manager = new ImageManager(['driver' => 'imagick']);
        $img = $manager->make(file_get_contents($file->getRealPath()));
        $localPath = BASE_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR;

        // 这里列举一些常用的操作,所有操作详见文档

        // 1、裁剪图片
        $img->crop(795, 793, 50, 40);
        // 2、调整尺寸
        $img->resize(330, 330, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // 3、...

        // 输出
        $img->save($localPath . 'gopher.heic', 95);

        return $this->result->getResult();
    }

    #[GetMapping(path: 'optimistic_locking')]
    public function optimisticLock(): array
    {
        /** @var Good $goodInfo */
        $goodInfo = Db::table('good')
            ->where(['g_name' => '红富士苹果'])
            ->select(['id', 'version', 'g_price'])
            ->first();
        $rows = Db::table('good')
            ->where(['id' => $goodInfo->id, 'version' => $goodInfo->version])
            ->where('g_inventory', '>', 0)
            ->decrement('g_inventory', 1, ['version' => (string) (intval($goodInfo->version) + 1)]);

        if ($rows !== 0) {
            (new SaleRecords([
                'gid' => $goodInfo->id,
                'order_no' => date('YmdHis') . uniqid(),
                'buyer' => uniqid(),
                'amount' => $goodInfo->g_price,
            ]))->save();

            return $this->result->getResult();
        }

        [$e, $m] = [ErrorCode::INVENTORY_ERR, ErrorCode::getMessage(ErrorCode::INVENTORY_ERR)];
        return $this->result
            ->setErrorInfo($e, $m)
            ->getResult();
    }

    #[GetMapping(path: 'share_mode_lock')]
    public function shareModeLock(): array
    {
        try {
            Db::beginTransaction();
            /** 上共享锁 @var Good $dove */
            $dove = Good::where(['g_name' => '德芙巧克力(200g)'])->sharedLock()->first();
            // 库存是否充足
            if ($dove->g_inventory > 0) {
                // 插入记录表购买记录
                (new SaleRecords([
                    'gid' => $dove->id,
                    'order_no' => date('YmdHis') . uniqid(),
                    'buyer' => uniqid(),
                    'amount' => $dove->g_price,
                ]))->save();
                // 扣减库存
                --$dove->g_inventory;
            } else {
                // 库存不足,提交事务,释放共享锁
                Db::commit();
                [$e, $m] = [ErrorCode::INVENTORY_ERR, ErrorCode::getMessage(ErrorCode::INVENTORY_ERR)];
                return $this->result->setErrorInfo($e, $m)->getResult();
            }
            // 更新数据
            $dove->save();
            // 提交事务,释放共享锁
            Db::commit();
        } catch (\Exception $e) {
            // 回滚,释放共享锁
            Db::rollBack();
            return $this->result->setErrorInfo($e->getCode(), $e->getMessage())->getResult();
        }

        return $this->result->getResult();
    }

    #[GetMapping(path: 'for_update_lock')]
    public function forUpdateLock(): array
    {
        try {
            Db::beginTransaction();
            /** @var Good $dove */
            $dove = Good::query()->where(['g_name' => '德芙巧克力(200g)'])->lockForUpdate()->first();

            if ($dove->g_inventory > 0) {
                (new SaleRecords([
                    'gid' => $dove->id,
                    'order_no' => date('YmdHis') . uniqid(),
                    'buyer' => uniqid(),
                    'amount' => $dove->g_price,
                ]))->save();

                --$dove->g_inventory;
            } else {
                Db::commit();
                [$e, $m] = [ErrorCode::INVENTORY_ERR, ErrorCode::getMessage(ErrorCode::INVENTORY_ERR)];
                return $this->result->setErrorInfo($e, $m)->getResult();
            }
            $dove->save();
            Db::commit();
        } catch (\Exception $e) {
            Db::rollBack();
            return $this->result->setErrorInfo($e->getCode(), $e->getMessage())->getResult();
        }

        return $this->result->getResult();
    }

    #[GetMapping(path: 'redis_lock')]
    public function redisLock(): array
    {
        $buyer = $this->request->input('buyer');

        // 创建当前协程的唯一值
        $clientID = uniqid();
        $isGetLock = RedisLock::muxLock(uniqueID: $clientID, key: $buyer);
        if (! $isGetLock) {
            [$e, $m] = [ErrorCode::GET_LOCK_ERR, ErrorCode::getMessage(ErrorCode::GET_LOCK_ERR)];
            return $this->result->setErrorInfo($e, $m)->getResult();
        }
        defer(function () use ($buyer, $clientID) {
            RedisLock::muxUnlock(uniqueID: $clientID, key: $buyer);
        });

        /** @var Good $dove */
        $dove = Good::query()->where(['g_name' => '德芙巧克力(200g)'])->first();
        if ($dove->g_inventory > 0) {
            (new SaleRecords([
                'gid' => $dove->id,
                'order_no' => date('YmdHis') . uniqid(),
                'buyer' => $buyer,
                'amount' => $dove->g_price,
            ]))->save();

            --$dove->g_inventory;
            $dove->save();
        } else {
            [$e, $m] = [ErrorCode::INVENTORY_ERR, ErrorCode::getMessage(ErrorCode::INVENTORY_ERR)];
            return $this->result->setErrorInfo($e, $m)->getResult();
        }

        return $this->result->getResult();
    }

    #[GetMapping(path: 'queue_lock')]
    public function rateLimit(): array
    {
        $driver = DriverFactory::getDriverInstance('limit-queue');
        $driver->push(new OversoldJob(uniqid(), []));
        return $this->result->getResult();
    }

    #[GetMapping(path: 'export_excel')]
    // 导出CSV同理,API都是统一的,Handler不一样而已
    public function exportExcel(): ResponseInterface
    {
        $excelHandler = new ExportExcelHandler();
        $excelHandler->setHeaders([
            'id', '商品ID', '订单号', '购买者', '价格', '创建时间', '变更时间',
        ]);
        SaleRecords::query()->orderBy('id')
            ->chunk(20, function ($records) use ($excelHandler) {
                $excelHandler->setData($records->toArray());
            });
        return $excelHandler->saveToBrowser('测试导出');
    }

    #[GetMapping(path: 'async_demo')]
    public function asyncDemo(): array
    {
        $driver = DriverFactory::getDriverInstance('redis-queue');
        for ($i = 0; $i < 10; ++$i) {
            go(function () use ($driver, $i) {
                for ($j = 0; $j < 200; ++$j) {
                    $driver->push(new StopDemoJob("group:{$i};index:{$j};", []));
                }
            });
        }
        return $this->result->getResult();
    }
}
