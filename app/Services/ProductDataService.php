<?php

namespace App\Services;

use App\Models\ProductData;
use Carbon\Carbon;

class ProductDataService
{

    public function insertAll(array $data) : void
    {
        ProductData::insert($this->preporationData($data));
    }

    public function preporationData(array $data) : array
    {
        $fieldMappings = $this->getFileMappings();
        $newData = array_map(function ($item) use ($fieldMappings) {
            // Создаем новый элемент массива
            $newItem = [];
            // Создаем массив с новыми ключами, используя маппинг
            $newKeys = array_map(function ($key) use ($fieldMappings) {
                return $fieldMappings[$key];
            }, array_keys($item));
            // Заполняем новый элемент массива соответствующими ключами и значениями
            foreach ($newKeys as $index => $newKey) {
                $newItem[$newKey] = $item[array_keys($item)[$index]];
            }
            $newItem = $this->addDatesInProduct($newItem);

            return $newItem;
        }, $data);

        return $newData;
    }

    public function getFileMappings() : array
    {
        // Определение соответствий полей CSV и полей базы данных
        $fieldMappings = [
            'Product Code' => 'strProductCode',
            'Product Name' => 'strProductName',
            'Product Description' => 'strProductDesc',
            'Stock' => 'intProductStock',
            'Cost in GBP' => 'decCostInGbp',
            'Discontinued' => 'dtmDiscontinued',
        ];
        return $fieldMappings;
    }

    public function addDatesInProduct(array $item) : array
    {
        $timestamp = Carbon::now();
        $item['stmTimestamp'] = $timestamp;
        $item['dtmAdded'] = $timestamp->format('Y-m-d H:i:s');
        return $item;
    }

}