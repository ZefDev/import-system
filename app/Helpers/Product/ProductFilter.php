<?php

namespace App\Helpers\Product;

class ProductFilter
{
    protected float $exchangeRate;
    protected array $rules;

    /**
     * Constructor.
     *
     * @param float $exchangeRate The exchange rate for currency conversion
     * @param array $rules The rules for filtering products
     */
    public function __construct(float $exchangeRate, array $rules)
    {
        $this->exchangeRate = $exchangeRate;
        $this->rules = $rules;
    }

    /**
     * Filter products based on the defined rules.
     *
     * @param array $data The data containing products to be filtered
     * @return array An array containing two lists: one for products that did not pass the rules and one for products that passed the rules
     */
    public function filterProducts(array $data): array
    {
        $listReport = []; // List of products that did not pass the rules
        $listForDB = []; // List of products that passed the rules

        // Iterate through each product
        foreach ($data as $item) {
            // Apply each rule to the product
            foreach ($this->rules as $rule) {
                // If the product fails the rule, add it to the list of products that did not pass and skip to the next product
                if ($rule->filter($item)) {
                    $listReport[] = $item;
                    continue 2; // Skip the rest of the rules for this item
                }
            }
            // If the product passes all rules, add it to the list of products that passed
            $listForDB[] = $item;
        }

        // Return both lists
        return [$listReport, $listForDB];
    }
}
