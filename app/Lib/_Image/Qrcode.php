<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/3/24 17:25
 * Author: JerryTian<tzfforyou@163.com>
 * File: Qrcode.php
 * Desc: https://packagist.org/packages/endroid/qr-code
 */


namespace App\Lib\_Image;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;

class Qrcode
{
    private BuilderInterface $builder;

    private string $encoding = 'UTF-8';

    private int $size;

    private int $margin;

    private string $logoPath;

    private string $labelText;

    private string $path;

    public function __construct(array $config = [])
    {
        isset($config['size']) ? $this->size = $config['size'] : $this->size = 300;
        isset($config['margin']) ? $this->margin = $config['margin'] : $this->margin = 10;
        isset($config['logoPath']) ? $this->logoPath = $config['logoPath'] : $this->logoPath = '';
        isset($config['labelText']) ? $this->labelText = $config['labelText'] : $this->labelText = '';
        isset($config['path']) ? $this->path = $config['path'] : $this->path = BASE_PATH . '/runtime/qrcode/';

        if (!is_dir($this->path)) {
            mkdir(iconv('GBK', 'UTF-8', $this->path), 0755);
        }

        $builder = Builder::create()
            ->writer(new PngWriter())
            ->encoding(new Encoding($this->encoding))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($this->size)
            ->margin($this->margin);
        if ($this->logoPath !== '') {
            $builder->logoPath($this->logoPath);
        }

        if ($this->labelText !== '') {
            $builder->labelText($this->labelText);
        }

        $this->builder = $builder;
    }

    // 输出二进制二维码
    public function getStream(string $content): string
    {
        return $this->builder->data($content)->build()->getString();
    }


    // 二维码保存到本地
    public function move(string $filename, string $content)
    {
        $file = $this->path . $filename;
        $this->builder->data($content)->build()->saveToFile($file);
    }
}