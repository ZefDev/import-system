<?php

namespace App\Helpers\CSVImport;

use App\Helpers\Product\ProductFilter;
use Exception;
use Illuminate\Support\Facades\Log;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use SplFileObject;

class CSVAnalyzer
{
    private Reader $reader;
    private string $path;
    protected array $data;
    protected ProductFilter $productFilter;

    public function __construct(string $path, ProductFilter $productFilter) {
        $this->path = $path;
        $this->productFilter = $productFilter;
    }

    public function analyze(): array
    {
        $csvData = array();
        $totalRows = 0;
        $list = array();
        $listError = array();

        $this->openFile($this->path);
        
        $this->reader->setHeaderOffset(0);
        $records = $this->reader->getRecords();
 
        foreach ($records as $offset => $record) {
            $totalRows++;
            if ($this->isValidRow($record)) {
                $list[] = $this->clean($record);
            } else {
                $listError[] = $this->clean($record);
            }
        }

        // Фильтруем продукты
        [$listReport, $listForDB] = $this->productFilter->filterProducts($list);

        $csvData['total'] = $totalRows;
        $csvData['successful'] = count($listForDB);
        $csvData['skipped'] = count($listReport) + count($listError);
        $csvData['listReport'] = $listReport;
        $csvData['listError'] = $listError;
        $csvData['listForDB'] = $listForDB;

        return $csvData;
    }

    private function isValidRow($row): bool
    {
        // Проверяем наличие null значений в элементах массива
        foreach ($row as $value) {
            if ($value === null) {
                return false;
            }
        }
        return true;
    }

    // открываем файл и устанавливаем ридер
    private function openFile(): void
    {
        $reader = Reader::createFromPath($this->path, 'r');
        $this->reader = $reader;
    }

    public function clean(array $record): array
    {
        foreach ($record as $key => $value) {
            $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), 'UTF-8', $value);
            $value = str_replace('$', '', $value);
            $value = trim($value);
            $value = $value === '' ? null : $value;
            $record[$key] = $value;
        }
        return $record;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    // Метод для преобразования содержимого CSV файла в UTF-8
    private function convertToUtf8(string $filePath): void
    {
        $content = file_get_contents($filePath);
        $encoding = mb_detect_encoding($content, mb_detect_order(), true);

        if ($encoding && strtoupper($encoding) !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // Записываем преобразованный CSV обратно в файл
            file_put_contents($filePath, $content);
        }
    }
}