<?php

namespace App\Helpers\CSVImport;

use App\Helpers\Product\ProductFilter;
use Exception;
use Illuminate\Support\Facades\Log;
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
    private function openFile()
    {
        $reader = Reader::createFromPath($this->path, 'r');
        $this->reader = $reader;
    }

    public function clean($record)
    {
        foreach ($record as $key => $value) {
            $value = str_replace('$', '', $value);
            $value = trim($value);
            $value = $value === '' ? null : $value;
            $record[$key] = $value;
        }
        return $record;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }
}