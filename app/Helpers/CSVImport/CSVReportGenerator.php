<?php

namespace App\Helpers\CSVImport;

class CSVReportGenerator
{
    public static function generate(array $analysis): string
    {
        $report = "Total rows: {$analysis['total']}\n" .
        "Successful rows: {$analysis['successful']}\n" .
        "Skipped rows: {$analysis['skipped']}\n";

        $report .= "Errors list: \n";
        foreach ($analysis['listError'] as $item) {
            $report .= implode(', ', $item) . "\n";
        }

        $report .= "Filters not passed: \n";
        foreach ($analysis['listReport'] as $item) {
            $report .= implode(', ', $item) . "\n";
        }

        return $report;
    }
}