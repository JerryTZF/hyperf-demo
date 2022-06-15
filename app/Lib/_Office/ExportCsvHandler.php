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
namespace App\Lib\_Office;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Response;
use Psr\Http\Message\ResponseInterface;

class ExportCsvHandler
{
    private string $fileData = '';

    /**
     * 设置表头.
     * @return $this
     * @example ['汽车品牌', '型号', '颜色', '价格', '经销商']
     */
    public function setHeaders(array $headers): static
    {
        $this->fileData .= self::UTF2GBK(implode(',', $headers)) . "\n";
        return $this;
    }

    /**
     * 添加数据.
     * @return bool|ExportCsvHandler
     * @example [['brand'=>'宝马','format'=>'X5','color'=>'BLACK','price'=>'54.12W','address'=>'深圳宝马4S店'],[],[]]
     */
    public function setData(array $data): static|bool
    {
        if (empty($this->fileData)) {
            return false;
        }

        foreach ($data as $index => $value) {
            $this->fileData .= self::UTF2GBK(implode(',', array_values($value))) . "\n";
        }

        return $this;
    }

    /**
     * 保存CSV到本地.
     * @return string[]
     */
    public function saveToLocal(string $filename): array
    {
        $filename = $filename . '.csv';
        $dir = BASE_PATH . '/runtime/storage/';
        if (! is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $outFileName = $dir . $filename;

        file_put_contents($outFileName, $this->fileData);
        return ['path' => $outFileName, 'filename' => $filename];
    }

    public function saveToBrowser(string $filename): ResponseInterface
    {
        $filename = $filename . '.csv';

        $contentType = 'text/csv';
        $response = new Response();

        return $response->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', $contentType)
            ->withHeader('content-disposition', "attachment; filename={$filename}")
            ->withHeader('content-transfer-encoding', 'binary')
            ->withHeader('pragma', 'public')
            ->withBody(new SwooleStream($this->fileData));
    }

    /**
     * utf-8 -> gbk.
     */
    public static function UTF2GBK(string $data): array|bool|string|null
    {
        return mb_convert_encoding($data, 'GBK', 'UTF-8');
    }
}
