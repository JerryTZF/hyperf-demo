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
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Http\Message\ResponseInterface;

#[Controller(prefix: 'oss')]
class OssController extends AbstractController
{
    #[GetMapping(path: 'download')]
    public function download(FilesystemFactory $factory): ResponseInterface
    {
        [$ossInstance, $fileName] = [
            $factory->get('oss'),
            $this->request->input('file'),
        ];
        if ($fileName === null) {
            throw new BusinessException(ErrorCode::FILE_NAME_ERR);
        }

        $remotePath = DIRECTORY_SEPARATOR . 'tiktok' . DIRECTORY_SEPARATOR . $fileName;
        $localPath = BASE_PATH . DIRECTORY_SEPARATOR . 'runtime' . DIRECTORY_SEPARATOR . $fileName;

        file_put_contents($localPath, $ossInstance->read($remotePath));

        return $this->response->download($localPath);
    }

    #[PostMapping(path: 'upload')]
    public function upload(FilesystemFactory $factory): array
    {
        $file = $this->request->file('file');
        $fileName = $this->request->input('file_name', $file->getClientFilename());

        [$remotePath, $endpoint, $bucket, $ossInstance] = [
            DIRECTORY_SEPARATOR . 'tiktok' . DIRECTORY_SEPARATOR . $fileName,
            env('OSS_ENDPOINT'),
            env('OSS_BUCKET'),
            $factory->get('oss'),
        ];

        $address = "https://{$bucket}.{$endpoint}/tiktok/" . $fileName;
        $ossInstance->write($remotePath, file_get_contents($file->getRealPath()));
        return $this->result->setData(['address' => $address])->getResult();
    }
}
