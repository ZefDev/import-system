<?php

namespace App\Console\Commands;

use App\Services\CSVImportService;
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

        $filePath = $this->argument('file');
        $mode = $this->option('mode');
        $mode = $mode === 'test' ? false : true;
        $csvImport = new CSVImportService();
        echo $csvImport->import($filePath, $mode);

        return 0;
    }
}
