<?php

namespace App\Console\Commands;

use App\Helpers\DataImporter;
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
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $exchangeRate = CurrencyService::getPriceCurrency('GBP');
        $dataImporter = new DataImporter('E:/Work/xampp2/htdocs/import-system/stock.csv', $exchangeRate);
        $dataImporter->openFile();
        $dataImporter->validateHeaders();
        $dataImporter->readDataFromFile();
        [$listReport, $listForDB] = $dataImporter->filterProducts();

        $product = new ProductDataService();
        $product->insertAll($listForDB);

        return 0;
    }
}
