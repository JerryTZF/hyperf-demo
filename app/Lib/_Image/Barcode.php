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
namespace App\Lib\_Image;

use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Barcode
{
    protected BarcodeGenerator $_generator;

    // 条码类型(类中定义的常量)
    protected mixed $_type;

    // 条码宽度(单根竖条宽度)
    protected int $_width;

    // 条码高度
    protected int $_height;

    // rgb
    protected array $_rgb;

    protected string $path;

    public function __construct(array $config = [])
    {
        $this->_generator = new BarcodeGeneratorPNG();
        isset($config['type']) ? $this->_type = $config['type'] : $this->_type = $this->_generator::TYPE_CODE_128;
        isset($config['width']) ? $this->_width = $config['width'] : $this->_width = 1;
        isset($config['height']) ? $this->_height = $config['height'] : $this->_height = 50;
        isset($config['rgb']) ? $this->_rgb = $config['rgb'] : $this->_rgb = [0, 0, 0];
        isset($config['path']) ? $this->path = $config['path'] : $this->path = BASE_PATH . '/runtime/barcode/';

        if (! is_dir($this->path)) {
            mkdir(iconv('GBK', 'UTF-8', $this->path), 0755);
        }
    }

    // 输出二进制条形码
    public function getStream(string $content = ''): string
    {
        return $this->_generator->getBarcode($content, $this->_type, $this->_width, $this->_height, $this->_rgb);
    }

    // 保存条码到本地
    public function move(string $filename, string $content): void
    {
        file_put_contents($this->path . $filename, $this->_generator->getBarcode($content, $this->_type, $this->_width, $this->_height, $this->_rgb));
    }
}
