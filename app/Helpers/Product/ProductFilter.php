<?php

namespace App\Helpers\Product;

use Carbon\Carbon;

class ProductFilter
{
    protected float $exchangeRate;
    protected array $rules;

    public function __construct(float $exchangeRate, array $rules)
    {
        $this->exchangeRate = $exchangeRate;
        $this->rules = $rules;
    }

    public function filterProducts(array $data): array
    {
        $listReport = [];
        $listForDB = [];

        foreach ($data as $item) {
            foreach ($this->rules as $rule) {
                if ($rule->filter($item)) {
                    $listReport[] = $item;
                    continue 2; // Пропустить остальные правила для этого элемента
                }
            }
            $item['Discontinued'] = $this->getDiscontinuedValue($item['Discontinued']);
            $item['Stock'] = intval($item['Stock']);

            $listForDB[] = $item;
        }

        return [$listReport, $listForDB];
    }

    public function getDiscontinuedValue($discontinued): ?string
    {
        return $discontinued === 'yes' ? Carbon::now()->toDateTimeString() : null;
    }

    public function isEmpty($value)
    {
        return $value === null || $value === '';
    }
}
