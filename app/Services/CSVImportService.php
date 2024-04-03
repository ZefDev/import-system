<?php

namespace App\Services;

use App\Helpers\CSVImport\CSVAnalyzer;
use App\Helpers\CSVImport\CSVImporter;
use App\Helpers\CSVImport\CSVReader;
use App\Helpers\CSVImport\CSVReportGenerator;
use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;
use App\Mapping\ProductMapping;
use App\Validators\ProductValidator;

class CSVImportService
{

    /**
     * Import data from CSV file.
     *
     * @param string $filePath Path to the CSV file
     * @param bool $mode Import mode
     * @return array Array with import result report
     */
    public function import(string $filePath, bool $mode): string
    {
        // Get data from CSV file
        $data = $this->getDataFromCSV($filePath);
        
        // If import mode is enabled, perform data insertion into database
        if ($mode) {
            $productsData = new ProductDataService();
            $productsData->insertAll($data['listForDB']);
        }
        
        // Generate import result report
        $report = CSVReportGenerator::generate($data);
        return $report;
    }

    /**
     * Get data from CSV file.
     *
     * @param string $filePath Path to the CSV file
     * @return array Array with data from the CSV file
     */
    protected function getDataFromCSV(string $filePath): array
    {
        // Read data from CSV file
        $csvReader = new CSVReader($filePath, (new ProductMapping)->getMapping());
        $dataProducts = $csvReader->readFile();
        
        // Get currency exchange rate
        $exchangeRate = CurrencyService::getPriceCurrency('GBP');
        
        // Create product filter
        $productFilter = new ProductFilter($exchangeRate, [
            new LowStockProductFilter($exchangeRate),
            new HighValueProductFilter($exchangeRate),
        ]);
        
        // Analyze data from CSV file using the product filter
        $csvAnalyzer = new CSVAnalyzer($productFilter, (new ProductValidator)->rulesForCSV());
        return $csvAnalyzer->analyze($dataProducts);
    }
}
