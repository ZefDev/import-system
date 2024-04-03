<?php

namespace Tests\Unit;

use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class ProductFilterTest extends TestCase
{
    /**
     * Test filterProducts method.
     *
     * @return void
     */
    public function testFilterProducts()
    {
        // Rules for filtering
        $rules = [
            new LowStockProductFilter(1.0),
            new HighValueProductFilter(1.0),
        ];

        // Input data
        $data = [
            ['strProductCode' => 'P001', 'dtmDiscontinued' => 'yes', 'intProductStock' => '10', 'decCostInGbp' => '10'],
            ['strProductCode' => 'P002', 'dtmDiscontinued' => '', 'intProductStock' => '1', 'decCostInGbp' => '1'],
            ['strProductCode' => 'P003', 'dtmDiscontinued' => 'yes', 'intProductStock' => '8', 'decCostInGbp' => '8'],
        ];

        // Create an instance of ProductFilter class
        $filter = new ProductFilter(1.0, $rules);

        // Call the filterProducts method
        [$listReport, $listForDB] = $filter->filterProducts($data);

        // Check the filtering results
        $this->assertEquals(1, count($listReport)); // Expecting only one record in the report
        $this->assertEquals(2, count($listForDB)); // Expecting two records for the database
    }
}
