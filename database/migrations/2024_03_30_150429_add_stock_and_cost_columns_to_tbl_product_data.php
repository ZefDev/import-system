<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockAndCostColumnsToTblProductData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_product_data', function (Blueprint $table) {
            $table->integer('intProductStock')->after('dtmDiscontinued');
            $table->decimal('decCostInGbp', 10, 2)->after('intProductStock');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_product_data', function (Blueprint $table) {
            $table->dropColumn('intProductStock');
            $table->dropColumn('decCostInGbp');
        });
    }
}
