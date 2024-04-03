<?php

namespace Tests\Unit;

use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testGetPriceCurrency()
    {
        // Set up a mock for the Http client
        Http::fake([
            '*' => Http::response([
                'rates' => [
                    'USD' => 1.2, // Assume 1 USD = 1.2
                    'EUR' => 0.8, // Assume 1 EUR = 0.8
                ]
            ], 200),
        ]);

        // Check that getPriceCurrency method returns the correct value for the given currency
        $usdPrice = CurrencyService::getPriceCurrency('USD');
        $this->assertEquals(1.2, $usdPrice);

        // Check that the method works correctly for another currency
        $eurPrice = CurrencyService::getPriceCurrency('EUR');
        $this->assertEquals(0.8, $eurPrice);

        // Check that the method returns 1 in case of error or if the currency is missing from the response
        Http::fake([
            '*' => Http::response([], 404), // Empty response with status code 404
        ]);
        $unknownCurrencyPrice = CurrencyService::getPriceCurrency('UNKNOWN');
        $this->assertEquals(1, $unknownCurrencyPrice);
    }
}
