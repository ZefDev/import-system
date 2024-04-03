<?php

namespace Tests\Unit;

use App\Helpers\CSVImport\CSVAnalyzer;
use App\Helpers\CSVImport\CSVReader;
use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;
use App\Mapping\ProductMapping;
use App\Services\CurrencyService;
use App\Validators\ProductValidator;
use Tests\TestCase;

class CSVAnalyzerTest extends TestCase
{
    protected CSVAnalyzer $csvAnalyzer;
    
    /**
     * Prepare objects for testing.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $exchangeRate = CurrencyService::getPriceCurrency('GBP');
        $productFilter = new ProductFilter($exchangeRate,[
            new LowStockProductFilter($exchangeRate),
            new HighValueProductFilter($exchangeRate),
        ]);
        
        $this->csvAnalyzer = new CSVAnalyzer($productFilter, (new ProductValidator)->rulesForCSV());
    }

    /**
     * Test CSV parsing.
     *
     * @return void
     */
    public function testCsvParsing()
    {
        // Create a temporary test CSV file with various records
        // Assume this code is already implemented in another method or class
        // Assume we have headers and data for the test file
        $headers = 'Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued';
        $csvData = [
            ['P0090', 'CD Bundle', 'Lots of fun', '10', '10', 'yes'],
            ['P0091', 'CD Bundle', 'Lots of 2', '10', '10', ''],
            ['P0092', 'CD Bundle', 'Lots of 3', '6', '10', 'yes'],
            // Add other records including incorrect ones if needed
        ];
        // Create a temporary CSV file
        $csvFilePath = $this->createCsvFile('test.csv', $headers, $csvData);
        $csvReader = new CSVReader($csvFilePath, (new ProductMapping)->getMapping());

        // Call the analyze() method
        $result = $this->csvAnalyzer->analyze($csvReader->readFile());
        // Check expected results, for example:
        $this->assertEquals(3, $result['total']); // Total number of records
        $this->assertEquals(3, $result['successful']); // Number of successfully processed records
        $this->assertEquals(0, $result['skipped']); // Number of skipped records
        // Check other expected results according to your logic
    }

    /**
     * Test data cleaning method.
     *
     * @return void
     */
    public function testCleanMethod()
    {
        // Create a data array to clean
        $record = ['   P0090  ', '10', '  Description  ', null];

        // Call the clean() method and expect the result
        $cleanedRecord = $this->csvAnalyzer->clean($record);

        // Check expected result after data cleaning
        $this->assertEquals(['P0090', '10', 'Description', null], $cleanedRecord);
    }

    // Additional tests including exception handling, product filtering, etc.

    /**
     * Helper method to create a temporary CSV file.
     *
     * @param string $fileName
     * @param string $headers
     * @param array $data
     * @return string
     */
    private function createCsvFile($fileName, $headers, $data)
    {
        $csvFilePath = storage_path('app/' . $fileName);
        
        // Write CSV headers
        file_put_contents($csvFilePath, $headers . PHP_EOL);

        // Write CSV data
        foreach ($data as $row) {
            file_put_contents($csvFilePath, implode(',', $row) . PHP_EOL, FILE_APPEND);
        }

        return $csvFilePath;
    }
}
