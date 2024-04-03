<?php

namespace App\Helpers\CSVImport;

use App\Interfaces\FileReportGeneratorInterface;

class CSVReportGenerator implements FileReportGeneratorInterface
{
    /**
     * Generate a report based on the analysis result.
     *
     * @param array $analysis The analysis result
     * @return string The generated report
     */
    public static function generate(array $analysis): string
    {
        // Generate the report header with total, successful, and skipped rows count
        $report = "Total rows: {$analysis['total']}\n" .
                  "Successful rows: {$analysis['successful']}\n" .
                  "Skipped rows: {$analysis['skipped']}\n";

        // Generate the list of errors
        $report .= "Errors list: \n";
        foreach ($analysis['listError'] as $item) {
            $report .= implode(', ', $item) . "\n";
        }

        // Generate the list of filters not passed
        $report .= "Filters not passed: \n";
        foreach ($analysis['listReport'] as $item) {
            $report .= implode(', ', $item) . "\n";
        }

        return $report;
    }
}
