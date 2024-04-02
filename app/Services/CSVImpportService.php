<?php

namespace App\Services;

use App\Helpers\CSVImport\CSVAnalyzer;
use App\Helpers\CSVImport\CSVImporter;
use App\Helpers\CSVImport\CSVReportGenerator;
use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;

class CSVImpportService
{

    public function import(string $filePath, bool $mode)
    {
        // Анализируем CSV файл
        $data = $this->getDataFromCSV($filePath);
        if($mode){
            $productsData = new ProductDataService();
            $productsData->insertAll($data['listForDB']);
            
        }
        // Генерируем отчет
        $report = CSVReportGenerator::generate($data);
        return $report;
    }

    protected function getDataFromCSV(string $filePath): array
    {
        //получаем текущий курс валюты для фильтра
        $exchangeRate = CurrencyService::getPriceCurrency('GBP');
        // Создаем экземпляры фильтров
        // Создаем экземпляр ProductFilter
        $productFilter = new ProductFilter($exchangeRate,[
            new LowStockProductFilter($exchangeRate),
            new HighValueProductFilter($exchangeRate),
        ]);
        //Запускаем анализ файла и получаем списки с добавлнием информации в бд, ошибок и не прошедшим по фильтру
        $analyzer = new CSVAnalyzer(
            $filePath,
            $productFilter
        );
        return $analyzer-> analyze();
    }
}