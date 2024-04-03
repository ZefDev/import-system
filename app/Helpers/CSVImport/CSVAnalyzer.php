<?php

namespace App\Helpers\CSVImport;

use App\Helpers\Product\ProductFilter;
use App\Interfaces\FileAnalyzerInterface;
use App\Validators\ProductValidator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CSVAnalyzer implements FileAnalyzerInterface
{
    protected ProductFilter $productFilter;
    protected array $rulesForValidation;

    /**
     * Constructor.
     *
     * @param ProductFilter $productFilter The product filter instance
     */
    public function __construct(ProductFilter $productFilter, array $rulesForValidation)
    {
        $this->productFilter = $productFilter;
        $this->rulesForValidation = $rulesForValidation;
    }

    /**
     * Analyze the CSV data.
     *
     * @param array $data The CSV data
     * @return array The analyzed data
     */
    public function analyze(array $data): array
    {
        $csvData = array();
        $list = array();
        $listError = array();
        
        // Iterate through each record in the CSV data
        foreach ($data as $record) {
            // Check if the row is valid
            if ($this->isValidRow($record) && !$this->isCSVValid($record)) {
                $list[] = $this->clean($record); // Add clean record to list
            } else {
                $listError[] = $this->clean($record); // Add clean record with errors to list
            }
        }
        
        // Filter the products
        [$listReport, $listForDB] = $this->productFilter->filterProducts($list);

        // Prepare the analyzed data
        $csvData['total'] = count($data);
        $csvData['successful'] = count($listForDB);
        $csvData['skipped'] = count($listReport) + count($listError);
        $csvData['listReport'] = $listReport;
        $csvData['listError'] = $listError;
        $csvData['listForDB'] = $listForDB;

        return $csvData;
    }

    /**
     * Check if the row is valid.
     *
     * @param array $row The row data
     * @return bool True if the row is valid, false otherwise
     */
    private function isValidRow($row): bool
    {
        // Check for null values in the row
        foreach ($row as $value) {
            if ($value === null) {
                return false;
            }
        }
        return true;
    }

    /**
     * Clean the record.
     *
     * @param array $record The record data
     * @return array The cleaned record
     */
    public function clean(array $record): array
    {
        // Iterate through each key-value pair in the record
        foreach ($record as $key => $value) {
            // Convert encoding to UTF-8
            $value = iconv(mb_detect_encoding($value, mb_detect_order(), true), 'UTF-8', $value);
            // Trim whitespace
            $value = trim($value);
            // Convert empty strings to null
            $value = $value === '' ? null : $value;
            // Update the record value
            $record[$key] = $value;
        }
        return $record;
    }

     /**
     * Check if the record fails validation based on CSV rules.
     *
     * @param array $record The record to be validated
     * @return bool Returns true if validation fails, otherwise false
     */
    private function isCSVValid(array $record): bool
    {
        return Validator::make($record, $this->rulesForValidation)->fails();
    }
}
