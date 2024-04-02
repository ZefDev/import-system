<?php

namespace Tests\Unit;

use App\Helpers\CSVImport\CSVReportGenerator;
use PHPUnit\Framework\TestCase;

class CSVReportGeneratorTest extends TestCase
{
    public function testGenerateReport()
    {
        $analysis = [
            'total' => 5,
            'successful' => 3,
            'skipped' => 2,
            'listError' => [
                ['Error 1'],
                ['Error 2'],
            ],
            'listReport' => [
                ['Report 1'],
                ['Report 2'],
            ],
        ];

        $expectedReport = "Total rows: 5\n" .
            "Successful rows: 3\n" .
            "Skipped rows: 2\n" .
            "Errors list: \n" .
            "Error 1\n" .
            "Error 2\n" .
            "Filters not passed: \n" .
            "Report 1\n" .
            "Report 2\n";

        $generatedReport = CSVReportGenerator::generate($analysis);

        $this->assertEquals($expectedReport, $generatedReport);
    }
}
