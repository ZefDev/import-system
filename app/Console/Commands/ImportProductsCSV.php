<?php

namespace App\Console\Commands;

use App\Helpers\CSVImport\CSVAnalyzer;
use App\Helpers\CSVImport\CSVReportGenerator;
use App\Helpers\DataImporter;
use App\Helpers\Product\DiscontinuedProductFilter;
use App\Helpers\Product\HighValueProductFilter;
use App\Helpers\Product\LowStockProductFilter;
use App\Helpers\Product\ProductFilter;
use App\Services\CSVImpportService;
use App\Services\CurrencyService;
use App\Services\ParserCSVService;
use App\Services\ProductDataService;
use Illuminate\Console\Command;

class ImportProductsCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products {file} {--mode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$filePath = $this->argument('file');
        $filePath = 'E:/Work/xampp2/htdocs/import-system/storage/app/test.csv';
        $csvImport = new CSVImpportService();
        echo $csvImport->import($filePath, true);

        return 0;
    }
}
