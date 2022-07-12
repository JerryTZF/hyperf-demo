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
namespace App\Lib\_AlibabaCloud;

use AlibabaCloud\SDK\ICE\V20201109\ICE;
use AlibabaCloud\SDK\ICE\V20201109\Models\RegisterMediaInfoRequest;
use Darabonba\OpenApi\Models\Config;

class IMS
{
    private ICE $client;

    public function __construct(array $configs = [])
    {
        $config = new Config();
        $config->accessKeyId = $configs['accessKeyId'] ?? env('IMS_ACCESS_ID', '');
        $config->accessKeySecret = $configs['accessKeySecret'] ?? env('IMS_ACCESS_SECRET', '');
        $config->regionId = $configs['regionId'] ?? env('IMS_REGIONID', '');
        $config->endpoint = $configs['endpoint'] ?? env('IMS_ENDPOINT', '');
        $this->client = new ICE($config);
    }

    public function RegisterMediaContent(
        string $fileUrl,
        string $title,
        string $mediaType = 'video',
        string $businessType = 'video',
    ): string {
        $request = new RegisterMediaInfoRequest();
        $request->inputURL = $fileUrl;
        $request->mediaType = $mediaType;
        $request->businessType = $businessType;
        $request->title = $title;
        $response = $this->client->registerMediaInfo($request);

        return $response->body->mediaId;
    }
}
