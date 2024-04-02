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
        // Устанавливаем заглушку для Http клиента
        Http::fake([
            '*' => Http::response([
                'rates' => [
                    'USD' => 1.2, // Предположим, что курс 1 USD = 1.2
                    'EUR' => 0.8, // Предположим, что курс 1 EUR = 0.8
                ]
            ], 200),
        ]);

        // Проверяем, что метод getPriceCurrency возвращает правильное значение для заданной валюты
        $usdPrice = CurrencyService::getPriceCurrency('USD');
        $this->assertEquals(1.2, $usdPrice);

        // Проверяем, что метод работает правильно для другой валюты
        $eurPrice = CurrencyService::getPriceCurrency('EUR');
        $this->assertEquals(0.8, $eurPrice);

        // Проверяем, что метод возвращает 1 в случае ошибки или если валюта отсутствует в ответе
        Http::fake([
            '*' => Http::response([], 404), // Пустой ответ с кодом 404
        ]);
        $unknownCurrencyPrice = CurrencyService::getPriceCurrency('UNKNOWN');
        $this->assertEquals(1, $unknownCurrencyPrice);
    }
}
