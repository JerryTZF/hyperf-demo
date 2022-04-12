<?php

declare(strict_types=1);

/**
 * Created by PhpStorm
 * Time: 2022/4/12 15:23
 * Author: JerryTian<tzfforyou@163.com>
 * File: ExportExcelHandler.php
 * Desc:
 */


namespace App\Lib\_Office;

use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Response;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use Psr\Http\Message\ResponseInterface;

class ExportExcelHandler
{
    private Spreadsheet $spreadsheet;

    private Worksheet $sheet;

    private int $row;

    public function __construct()
    {
        // Create a Spreadsheet object
        $this->spreadsheet = new Spreadsheet();
        // Setting a spreadsheet's metadata
        $this->spreadsheet->getProperties()
            ->setCreator('Jerry')
            ->setLastModifiedBy('Jerry')
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Excel");
        // Sets the active sheet index to the first sheet
        $this->spreadsheet->setActiveSheetIndex(0);
        // Active sheet
        $this->sheet = $this->spreadsheet->getActiveSheet();
        // Set name
        $this->spreadsheet->getActiveSheet()->setTitle('Sheet1');
    }

    /**
     * 设置表头
     * @param array $title
     * @return $this
     * @example ['汽车品牌', '型号', '颜色', '价格', '经销商']
     */
    public function setHeaders(array $title): static
    {
        foreach ($title as $k => $item) {
            $this->sheet->setCellValue(chr($k + 65) . '1', $item);
        }
        $this->row = 2;
        return $this;
    }

    /**
     * 添加数据
     * @param array $data
     * @return $this
     * @example [['宝马','X5','BLACK','54.12W','深圳宝马4S店'],[],[]]
     */
    public function setData(array $data): static
    {
        foreach ($data as $datum) {
            $col = 'A';
            foreach ($datum as $value) {
                // write col
                $this->sheet->setCellValue($col . $this->row, $value);
                $col++;
            }
            $this->row++;
        }
        return $this;
    }

    /**
     * 保存到本地
     * @param string $filename
     * @return array
     */
    public function saveToLocal(string $filename): array
    {
        $this->spreadsheet->setActiveSheetIndex(0);

        $filename = $filename . '.xlsx';
        $dir = BASE_PATH . '/runtime/storage/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $outFileName = $dir . $filename;
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save($outFileName);

        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);

        return ['path' => $outFileName, 'filename' => $filename];
    }

    /**
     * 输出到浏览器
     * @param string $filename
     * @return ResponseInterface
     * @throws Exception
     */
    public function saveToBrowser(string $filename): ResponseInterface
    {
        $filename = $filename . '.xlsx';
        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save('./tmp.xlsx');

        $content = file_get_contents('./tmp.xlsx');
        // 删除临时文件
        unlink('./tmp.xlsx');

        $response = new Response();

        return $response->withHeader('content-description', 'File Transfer')
            ->withHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->withHeader('content-disposition', "attachment; filename={$filename}")
            ->withHeader('content-transfer-encoding', 'binary')
            ->withBody(new SwooleStream((string)$content));
    }
}