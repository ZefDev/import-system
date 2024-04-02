<?php

namespace Tests\Unit;

use App\Helpers\CSVImport\CSVAnalyzer;
use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;
use App\Services\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CSVAnalyzerTest extends TestCase
{
    protected CSVAnalyzer $csvAnalyzer;
    // Подготовка объектов для тестирования
    protected function setUp(): void
    {
        parent::setUp();
        
        $exchangeRate = CurrencyService::getPriceCurrency('GBP');
        $productFilter = new ProductFilter($exchangeRate,[
            new LowStockProductFilter($exchangeRate),
            new HighValueProductFilter($exchangeRate),
        ]);
        
        $this->csvAnalyzer = new CSVAnalyzer('test.csv', $productFilter);
    }

    // Проверка обработки правильных и неправильных записей CSV
    public function testCsvParsing()
    {
        // Создаем временный тестовый CSV файл с различными записями
        // Допустим, этот код уже реализован в другом методе или классе
        // Предположим, что у нас есть заголовки и данные для тестового файла
        $headers = 'Product Code,Product Name,Product Description,Stock,Cost in GBP,Discontinued';
        $csvData = [
            ['P0090', 'CD Bundle', 'Lots of fun', '10', '10', 'yes'],
            ['P0091', 'CD Bundle', 'Lots of 2', '10', '10', ''],
            ['P0092', 'CD Bundle', 'Lots of 3', '6', '10', 'yes'],
            // Добавьте другие записи, включая неправильные, если нужно
        ];

        // Создаем временный CSV файл
        $csvFilePath = $this->createCsvFile('test.csv', $headers, $csvData);

        // Подменяем путь к файлу на временный путь в объекте CSVAnalyzer
        $this->csvAnalyzer->setPath($csvFilePath);

        // Вызываем метод analyze()
        $result = $this->csvAnalyzer->analyze();
        // Проверяем ожидаемые результаты, например:
        $this->assertEquals(3, $result['total']); // Общее количество записей
        $this->assertEquals(3, $result['successful']); // Количество успешно обработанных записей
        $this->assertEquals(0, $result['skipped']); // Количество пропущенных записей
        // Проверьте другие ожидаемые результаты в соответствии с вашей логикой
    }

    // Проверка обработки исключений
    public function testExceptionHandling()
    {
        // Подготавливаем объект CSVAnalyzer с некорректным путем к файлу
        $this->csvAnalyzer->setPath('nonexistent.csv');

        // Вызываем метод analyze() и ожидаем исключение
        $this->expectException(\Exception::class);

        // Ожидаем, что метод analyze() выбросит исключение
        $this->csvAnalyzer->analyze();
    }

    // Проверка очистки данных
    public function testCleanMethod()
    {
        // Создаем массив данных для очистки
        $record = ['   P0090  ', '$10', '  Description  ', null];

        // Вызываем метод clean() и ожидаемый результат
        $cleanedRecord = $this->csvAnalyzer->clean($record);

        // Проверяем ожидаемый результат после очистки данных
        $this->assertEquals(['P0090', '10', 'Description', null], $cleanedRecord);
    }

    // Дополнительные тесты, включая обработку исключений, фильтрацию продуктов и т. д.

    // Вспомогательный метод для создания временного CSV файла
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