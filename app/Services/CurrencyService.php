<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{

    public static function getPriceCurrency($currency)
    {
        $response = Http::get(env('CURRENCY_API_URL'), [
            'app_id' => env('CURRENCY_API_KEY'),
        ]);

        if (!$response->ok() || !isset($response['rates'][$currency])) {
            return 1; // если api не работает вернём 1 для расчета цены
        }

        return round($response['rates'][$currency], 2);
    }
}
