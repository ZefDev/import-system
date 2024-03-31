<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductData extends Model
{
    use HasFactory;

    protected $table = 'tbl_product_data';

    protected $fillable = [
        'intProductDataId',
        'strProductName',
        'strProductDesc',
        'strProductCode',
        'dtmAdded',
        'dtmDiscontinued',
        'intProductStock',
        'decCostInGbp',
        'stmTimestamp',
    ];
}
