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

use App\Lib\_AlibabaCloud\IMS;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'ims')]
class ImsController extends AbstractController
{
    #[GetMapping(path: 'test')]
    public function test(): array
    {
        $ims = new IMS();
        $mediaId = $ims->RegisterMediaContent(
            fileUrl: 'https://jerry-video.oss-cn-shenzhen.aliyuncs.com/tiktok/Lisa_HLH.mp4',
            title: 'Lisa-红莲华'
        );

        return $this->result->setData(['mediaId' => $mediaId])->getResult();
    }
}
