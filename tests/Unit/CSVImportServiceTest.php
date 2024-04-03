<?php

namespace Tests\Unit;

use App\Services\CSVImportService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CSVImportServiceTest extends TestCase
{
    /**
     * A basic unit test import.
     *
     * @return void
     */
    public function testImport()
    {
        $csvFilePath = storage_path('app/test.csv');
        file_put_contents($csvFilePath, "Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued\nP001,Product 1,Description 1,10,20,yes\nP002,Product 2,Description 2,5,15,no\nP003,Product 3,Description 3,8,25,yes\n");

        Http::fake([
            '*' => Http::response(['rates' => ['GBP' => 1.0]], 200),
        ]);

        $importService = new CSVImportService();

        $report = $importService->import($csvFilePath, true);

        $this->assertStringContainsString('Total rows: 3', $report);
    }
    
}
