<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportProductCsvTest extends TestCase
{
    /**
     * Test CSV parsing.
     *
     * @return void
     */
    public function testCsvParsing()
    {
        // CSV file headers
        $headers = 'Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued';

        // CSV file data
        $csvData = [
            ['P0095', 'CD Bundle', 'Lots of fun', '10', '10', 'yes'],
            ['P0091', 'CD Bundle', 'Lots of 2', '10', '10', ''],
            ['P0092', 'CD Bundle', 'Lots of 3', '6', '10', 'yes'],
        ];

        // Create a temporary test CSV file
        $csvFileName = 'test.csv';
        $csvFilePath = $this->createCsvFile($csvFileName, $headers, $csvData);

        // Create UploadedFile from the temporary file
        $uploadedFile = new UploadedFile($csvFilePath, $csvFileName);

        // Upload the CSV file and call your console command
        $this->artisan('import:products', [
            'file' => $uploadedFile->getPathname(),
        ])->assertExitCode(0);

        // Check that specific data was successfully saved to the database
        $this->assertDatabaseHas('tbl_product_data', [
            'strProductCode' => 'P0095',
        ]);
    }

    /**
     * Create a CSV file.
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
