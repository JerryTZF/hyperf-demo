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

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller(prefix: 'ffmpeg')]
class FFMpegController extends AbstractController
{
    #[GetMapping(path: 'test')]
    public function demo(): array
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
            'ffprobe.binaries' => '/usr/local/bin/ffprobe',
            'timeout' => 3600,
            'ffmpeg.threads' => 8,
            'temporary_directory' => '/Users/tianchaofan/Tmp',
        ]);

        // 打开视频
        $video = $ffmpeg->open(BASE_PATH . '/orignal.mp4');

        // 详细操作参见: https://github.com/PHP-FFMpeg/PHP-FFMpeg
        go(function () use ($video) {
            // 提取某一帧
            $frame = $video->frame(TimeCode::fromSeconds(1));
            $frame->save(BASE_PATH . '/cover.jpg');

            // 转码
            $format = new X264();
            $format->on('progress', function ($video, $format, $percentage) {
                echo "{$percentage} % 已转码 \n";
            });

            $format->setKiloBitrate(5000)->setAudioChannels(2)->setAudioKiloBitrate(256);
            $video->save($format, BASE_PATH . '/FFMpeg/transcoded.mp4');

            echo "完成转码\n";
        });

        return $this->result->getResult();
    }
}
