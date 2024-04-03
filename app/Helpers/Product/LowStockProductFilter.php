<?php

namespace App\Helpers\Product;

use App\Interfaces\ProductFilterRule;

class LowStockProductFilter implements ProductFilterRule
{
    protected $exchangeRate;

    /**
     * Constructor.
     *
     * @param float $exchangeRate The exchange rate for currency conversion
     */
    public function __construct($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Filter the product item based on its cost in GBP converted to the local currency and stock quantity.
     *
     * @param array $item The product item
     * @return bool True if the product cost in local currency is less than 5 and stock quantity is less than 10, false otherwise
     */
    public function filter(array $item): bool
    {
        // Check if the product cost from GBP to local currency is less than 5 and stock quantity is less than 10
        return (floatval($item['decCostInGbp']) / $this->exchangeRate) < 5 && $item['intProductStock'] < 10;
    }
}
