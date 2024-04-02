<?php

namespace Tests\Unit;

use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ProductFilterTest extends TestCase
{
    public function testFilterProducts()
    {
        $rules = [
            new LowStockProductFilter(1.0),
            new HighValueProductFilter(1.0),
        ];

        // Входные данные
        $data = [
            ['Product Code' => 'P001', 'Discontinued' => 'yes', 'Stock' => '10', 'Cost in GBP' => '10'],
            ['Product Code' => 'P002', 'Discontinued' => '', 'Stock' => '1', 'Cost in GBP' => '1'],
            ['Product Code' => 'P003', 'Discontinued' => 'yes', 'Stock' => '8', 'Cost in GBP' => '8'],
        ];

        // Создаем экземпляр класса ProductFilter
        $filter = new ProductFilter(1.0, $rules);

        // Вызываем метод filterProducts()
        [$listReport, $listForDB] = $filter->filterProducts($data);

        // Проверяем результаты фильтрации
        $this->assertEquals(1, count($listReport)); // Ожидаем только одну запись в отчете
        $this->assertEquals(2, count($listForDB)); // Ожидаем две записи для базы данных
    }

    public function testGetDiscontinuedValue()
    {
        // Создаем экземпляр класса ProductFilter
        $filter = new ProductFilter(1.0, []);

        // Проверяем, что метод возвращает текущую дату и время для "yes"
        $this->assertEquals(Carbon::now()->toDateTimeString(), $filter->getDiscontinuedValue('yes'));

        // Проверяем, что метод возвращает null для любого другого значения
        $this->assertNull($filter->getDiscontinuedValue('no'));
        $this->assertNull($filter->getDiscontinuedValue(null));
        $this->assertNull($filter->getDiscontinuedValue(''));
    }
}
