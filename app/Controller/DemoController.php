<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/25 11:22
 * Author: JerryTian<tzfforyou@163.com>
 * File: DemoController.php
 * Desc:
 */


namespace App\Controller;

use App\Constants\CacheKeys;
use App\Constants\ErrorCode;
use App\Lib\_Cache\Cache;
use App\Lib\_Validator\DemoValidator;
use App\Middleware\CheckTokenMiddleware;
use App\Service\DemoService;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\Arr;
use Hyperf\Utils\Str;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: "demo")]
class DemoController extends AbstractController
{
    #[Inject]
    protected DemoService $service;

    // 演示异地登录 + 缓存
    #[PostMapping(path: "get_admin_info")]
    #[Middleware(CheckTokenMiddleware::class)]
    public function getAdminInfo(): array
    {
        $account = $this->request->input('account');
        $uuid = $this->request->input('uuid');
        return $this->service->getAdminInfo($account, $uuid);
    }

    // 演示更新数据刷新缓存
    #[PostMapping(path: "update_admin_info")]
    #[Middleware(CheckTokenMiddleware::class)]
    public function updateAdminInfo(): array
    {
        $account = $this->request->input('account');
        $password = $this->request->input('password');
        return $this->service->updateAdminInfo($account, $password);
    }

    #[PostMapping(path: "cache")]
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
            'single'   => $cacheData,
            'multiple' => $multipleData
        ])->getResult();
    }

    // 重定向演示
    #[GetMapping(path: "redirect_2_wiki")]
    public function redirect(): ResponseInterface
    {
        return $this->response->redirect('https://wiki.tzf-foryou.xyz');
    }

    // 演示文件系统(阿里云OSS)
    #[PostMapping(path: "oss")]
    public function file(FilesystemFactory $factory): array|ResponseInterface
    {
        // 获取阿里云OSS适配器
        $ossInstance = $factory->get('oss');
        $action = $this->request->input('action', 'get');

        DemoValidator::ossValidator(['action' => $action]);

        if ($action === 'get') {
            $fileName = $this->request->input('file_name');
            if (null === $fileName) {
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
                in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'heic', 'PNG', 'JPG', 'JPEG', 'HEIC'])
            ];
            if (!$isMimeRight || !$isExtensionRight) {
                [$e, $m] = [ErrorCode::FILE_MIME_ERR, ErrorCode::getMessage(ErrorCode::FILE_MIME_ERR)];
                return $this->result->setErrorInfo($e, $m)->getResult();
            }

            // 写入OSS
            $fileName = 'UPLOADER_' . date('YmdHis') . '.' . $file->getExtension();
            $remotePath = DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . $fileName;
            $address = 'https://jerry-markdown.oss-cn-shenzhen.aliyuncs.com/img/' . $fileName;
            $ossInstance->write($remotePath, file_get_contents($file->getRealPath()));
            return $this->result->setData(['address' => $address])->getResult();
        }

        return $this->result->getResult();
    }
}