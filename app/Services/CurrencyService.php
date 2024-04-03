<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    /**
     * Get the price of the currency.
     *
     * @param string $currency The currency code
     * @return float The price of the currency
     */
    public static function getPriceCurrency($currency)
    {
        // Send a GET request to the currency API to fetch currency rates
        $response = Http::get(env('CURRENCY_API_URL'), [
            'app_id' => env('CURRENCY_API_KEY'),
        ]);

        // Check if the response is successful and if the currency rate exists
        if (!$response->ok() || !isset($response['rates'][$currency])) {
            // If the API is not working, return 1 for price calculation
            return 1;
        }

        // Return the rounded price of the currency
        return round($response['rates'][$currency], 2);
    }
}
