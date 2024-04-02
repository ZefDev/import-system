<?php

namespace App\Helpers\Product;

use App\Interfaces\ProductFilterRule;

class HighValueProductFilter implements ProductFilterRule
{
    protected $exchangeRate;

    public function __construct($exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    public function filter(array $item): bool
    {
        return (floatval($item['Cost in GBP']) / $this->exchangeRate) > 1000;
    }
}