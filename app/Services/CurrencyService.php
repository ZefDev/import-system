<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    protected static string $link = 'https://openexchangerates.org/api/latest.json?app_id=a7465cc01b4e4d2da8438afd0c73745f';
    protected static string $app_id = 'a7465cc01b4e4d2da8438afd0c73745f';

    public static function getPriceCurrency($currency)
    {
        $response = Http::get(self::$link);
        // , [
        //     'query' => [
        //         'app_id' => $this->app_id,
        //     ]
        // ]

        $data = $response->json();

        if(!$response->ok() || !isset($data['rates'])) {
            return false;
        }

        return round($data['rates'][$currency], 2);
    }
}
