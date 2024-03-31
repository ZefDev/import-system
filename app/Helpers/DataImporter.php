<?php

namespace App\Helpers;

use App\Interfaces\DataImporterInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class DataImporter
{
    private Reader $reader;
    private array $originalHeaders = ['Product Code', 'Product Name', 'Product Description', 'Stock', 'Cost in GBP', 'Discontinued'];
    private string $path;
    protected array $data;
    private float $exchangeRate; // Добавляем свойство для хранения курса валюты

    public function __construct(string $path, float $exchangeRate) {
        $this->path = $path;
        $this->exchangeRate = $exchangeRate; // Сохраняем переданный курс валюты
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

    public function filterProducts(): array
    {
        $listReport = [];
        $listForDB = [];

        foreach ($this->data as $item) {
            if ($this->shouldBeDiscontinued($item)) {
                $item['Discontinued'] = Carbon::now()->toDateTimeString(); // или NOW() для MySQL
            } else {
                $item['Discontinued'] = null; // Установка значения NULL, если товар не снят с производства
            }
            $item['Stock'] = intval($item['Stock']);
            // Добавляем проверку на пустую строку для intProductStock
            if ($this->isEmpty($item['Stock'])) {
                $item['Stock'] = 0; // Устанавливаем значение NULL, если строка пустая
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
        return (floatval($item['Cost in GBP']) / $this->exchangeRate) < 5 && $item['Stock'] < 10;
    }

    protected function shouldExcludeHighValue($item)
    {
        return (floatval($item['Cost in GBP']) / $this->exchangeRate) > 1000;
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
    public function readDataFromFile(): void
    {
        $list = array();
        $this->reader->setHeaderOffset(0);
        $records = $this->reader->getRecords();

        foreach ($records as $offset => $record) {
            $list[] = $this->clean($record);
        }
        $this->data = $list;
    }

    public function validateData(): void
    {
        
    }

    protected function clean($record)
    {
        foreach ($record as $key => $value) {
            // Удаляем символ "$" из строки, если он есть
            $value = str_replace('$', '', $value);
            //$value = utf8_encode($value);
            $value = trim($value);
            $value = $value === '' ? null : $value;
            $record[$key] = $value;
        }
        return $record;
    }

    protected function isEmpty($value)
    {
        return $value === '' || $value === null; // Проверяем на пустую строку или NULL
    }
}