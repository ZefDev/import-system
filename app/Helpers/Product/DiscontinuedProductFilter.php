<?php

namespace App\Helpers\Product;

use App\Interfaces\ProductFilterRule;

class DiscontinuedProductFilter implements ProductFilterRule
{
    /**
     * Filter the product item based on the discontinued status.
     *
     * @param array $item The product item
     * @return bool True if the product is discontinued, false otherwise
     */
    public function filter(array $item): bool
    {
        // Check if the product is discontinued
        return $item['dtmDiscontinued'] === 'yes';
    }
}
