<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportProductCsvTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testCsvParsing()
    {
        // Заголовки CSV файла
        $headers = 'Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued';

        // Данные CSV файла
        $csvData = [
            ['P0090', 'CD Bundle', 'Lots of fun', '10', '10', 'yes'],
            ['P0091', 'CD Bundle', 'Lots of 2', '10', '10', ''],
            ['P0092', 'CD Bundle', 'Lots of 3', '6', '10', 'yes'],
        ];

        // Создаем временный тестовый CSV файл
        $csvFileName = 'test.csv';
        $csvFilePath = $this->createCsvFile($csvFileName, $headers, $csvData);

        // Создаем UploadedFile из временного файла
        $uploadedFile = new UploadedFile($csvFilePath, $csvFileName);

        // Загружаем CSV файл и вызываем вашу консольную команду
        $this->artisan('import:products', [
            'file' => $uploadedFile->getPathname(),
        ])->assertExitCode(0);

        // Проверяем, что определенные данные были успешно сохранены в базе данных
        $this->assertDatabaseHas('tbl_product_data', [
            'strProductCode' => 'P0090',
        ]);
    }

    private function createCsvFile($fileName, $headers, $data)
    {
        $csvFilePath = storage_path('app/' . $fileName);
        
        // Записываем заголовки CSV
        file_put_contents($csvFilePath, $headers . PHP_EOL);

        // Записываем данные CSV
        foreach ($data as $row) {
            file_put_contents($csvFilePath, implode(',', $row) . PHP_EOL, FILE_APPEND);
        }

        return $csvFilePath;
    }
}
