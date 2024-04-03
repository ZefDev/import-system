<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

class ProductValidator
{
    public function rules(): array
    {
        return [
            'strProductName' => 'required|string|max:50',
            'strProductDesc' => 'required|string|max:255',
            'strProductCode' => 'required|string|max:10|unique:tbl_product_data',
            'dtmAdded' => 'nullable|date',
            'dtmDiscontinued' => 'nullable|date',
            'intProductStock' => 'required|numeric',
            'decCostInGbp' => 'required|numeric',
        ];
    }

    public function rulesForCSV(): array
    {
        return [
            'strProductName' => 'required|string|max:50',
            'strProductDesc' => 'required|string|max:255',
            'strProductCode' => 'required|string|max:10|unique:tbl_product_data',
            'dtmDiscontinued' => 'nullable|string',
            'intProductStock' => 'required|numeric',
            'decCostInGbp' => 'required|numeric',
        ];
    }
}
