<?php

namespace App\Console\Commands;

use App\Helpers\DataImporter;
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
        $dataImporter = new DataImporter('E:/Work/xampp2/htdocs/import-system/stock.csv');
        $dataImporter->openFile();
        $dataImporter->validateHeaders();
        $data = $dataImporter->readDataFromFile();
        
        $service = new ProductDataService();
        $service->insertAll($data);

        return 0;
    }
}
