<?php

namespace App\Helpers;

use App\Interfaces\DataImporterInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DataImporter implements DataImporterInterface
{
    private Reader $reader;
    private array $originalHeaders = ['Product Code', 'Product Name', 'Product Description', 'Stock', 'Cost in GBP', 'Discontinued'];
    private string $path;
    protected array $data;

    public function __construct(string $path) {
        $this->path = $path;
    }
    // открываем файл и устанавливаем ридер
    public function openFile()
    {
        $reader = Reader::createFromPath($this->path, 'r');
        $this->reader = $reader;
    }

    // проверка заголовков
    public function validateHeaders(): void
    {
        $headersFromFile = array_values($this->reader->fetchOne());
        if($this->originalHeaders !== $headersFromFile) {
            Log::error('Expected: ' . implode(",", $this->originalHeaders));
            Log::error('Actual: ' . implode(",", $headersFromFile));
            throw new Exception("Заголовки не совпадают. Проверьте логи.");
        }
    }

    public function filterProducts()
    {
        $listReport = [];
        $listForDB = [];

        foreach ($this->data as $item) {
            if ($this->shouldBeDiscontinued($item)) {
                $item['Discontinued'] = Carbon::now();
            }

            if ($this->shouldExclude($item)) {
                $listReport[] = $item;
            } else if ($this->shouldExcludeHighValue($item)) {
                $listReport[] = $item;
            } else {
                $listForDB[] = $item;
            }
        }

        return [$listReport, $listForDB];
    }

    protected function shouldBeDiscontinued($item)
    {
        return $item['Discontinued'] === 'yes';
    }

    protected function shouldExclude($item)
    {
        return $item['Cost in GBP'] < 5 && $item['Stock'] < 10;
    }

    protected function shouldExcludeHighValue($item)
    {
        return $item['Cost in GBP'] > 1000;
    }

    function isCSVFormattedCorrectly($filePath)
    {
        if (!file_exists($filePath)) {
            return false; // Файл не существует
        }

        $handle = fopen($filePath, "r");
        if ($handle === false) {
            return false; // Не удалось открыть файл
        }

        $isValid = true;

        // Проверяем каждую строку файла CSV
        while (($data = fgetcsv($handle)) !== false) {
            // Проверяем количество элементов в строке
            $numFields = count($data);
            if ($numFields != 5) { // Например, ожидаем три элемента в строке
                $isValid = false;
                break;
            }

            // Другие проверки формата, если необходимо
            // Например, можно проверить, что каждый элемент строк является числом или строкой и т.д.

            // Можно добавить другие проверки по мере необходимости
        }

        fclose($handle);

        return $isValid;
    }
    // получение данных из файла
    public function readDataFromFile(): array
    {
        $list = array();
        $this->reader->setHeaderOffset(0);
        $records = $this->reader->getRecords();
        var_dump($records); die();
        foreach ($records as $offset => $record) {
            $list[] = $record;
        }
        return $list;
    }

    public function validateData(): void
    {
        
    }

    public function clean($record)
    {
        foreach ($record as $key => $value) {
            $value = utf8_encode($value);
            $value = trim($value);
            $value = $value === '' ? null : $value;
            $record[$key] = $value;
        }
        return $record;
    }
}