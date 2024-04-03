<?php

namespace App\Mapping;

class ProductMapping
{
    public function getMapping()
    {
        return [
            'Product Code' => 'strProductCode',
            'Product Name' => 'strProductName',
            'Product Description' => 'strProductDesc',
            'Stock' => 'intProductStock',
            'Cost in GBP' => 'decCostInGbp',
            'Discontinued' => 'dtmDiscontinued',
        ];
    }
}
