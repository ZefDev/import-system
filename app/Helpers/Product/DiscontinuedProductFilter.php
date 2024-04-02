<?php

namespace App\Helpers\Product;

use App\Interfaces\ProductFilterRule;

class DiscontinuedProductFilter implements ProductFilterRule
{
    public function filter(array $item): bool
    {
        return $item['Discontinued'] === 'yes';
    }
}