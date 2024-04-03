<?php

namespace App\Helpers\Product;

use App\Interfaces\ProductFilterRule;

class HighValueProductFilter implements ProductFilterRule
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
     * Filter the product item based on its cost in GBP converted to the local currency.
     *
     * @param array $item The product item
     * @return bool True if the product cost in local currency is higher than 1000, false otherwise
     */
    public function filter(array $item): bool
    {
        // Convert the product cost from GBP to local currency and check if it's higher than 1000
        return (floatval($item['decCostInGbp']) / $this->exchangeRate) > 1000;
    }
}
