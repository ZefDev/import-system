<?php

namespace App\Services;

use App\Mapping\ProductMapping;
use App\Models\ProductData;
use App\Validators\ProductValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductDataService
{

    /**
     * Insert all validated data into the database.
     *
     * @param array $data The data to be inserted
     * @return void
     */
    public function insertAll(array $data): void
    {
        // Prepare and validate the data
        $data = $this->preparationData($data);
        $validatedData = $this->validateData($data);

        // If there is valid data, insert it into the database
        if (!empty($validatedData)) {
            ProductData::insert($validatedData);
        }
    }

    /**
     * Prepare the data for insertion.
     *
     * @param array $data The data to be prepared
     * @return array The prepared data
     */
    public function preparationData(array $data): array
    {
        $newData = [];
        // Iterate through the data and prepare it for insertion
        foreach ($data as $item) {
            // Set discontinued date if applicable
            $item['dtmDiscontinued'] = $this->getDiscontinuedValue($item['dtmDiscontinued']);
            // Convert product stock to integer
            $item['intProductStock'] = intval($item['intProductStock']);
            // Add current dates in the product item
            $item = $this->addDatesInProduct($item);
            $newData[] = $item;
        }

        return $newData;
    }

    /**
     * Add current dates to the product item.
     *
     * @param array $item The product item
     * @return array The product item with added dates
     */
    public function addDatesInProduct(array $item): array
    {
        $timestamp = Carbon::now();
        $item['stmTimestamp'] = $timestamp;
        $item['dtmAdded'] = $timestamp->format('Y-m-d H:i:s');
        return $item;
    }

    /**
     * Get the discontinued value based on the input.
     *
     * @param mixed $discontinued The discontinued value
     * @return string|null The discontinued value as a string or null
     */
    public function getDiscontinuedValue($discontinued): ?string
    {
        return $discontinued === 'yes' ? Carbon::now()->toDateTimeString() : null;
    }

    /**
     * Validate the data.
     *
     * @param array $data The data to be validated
     * @return array The validated data
     */
    protected function validateData(array $data): array
    {
        $collection = new Collection($data);

        // Reject invalid records using the product validator
        $validRecords = $collection->reject(function ($record) {
            return Validator::make($record, (new ProductValidator)->rules())->fails();
        });

        // Convert the validated records to an array and return them
        $filteredData = $validRecords->toArray();
        return $filteredData;
    }

}
