<?php

namespace App\Http\Controllers;

use App\Services\MarketData\MarketDataGateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    protected $priceService;

    public function __construct(MarketDataGateway $priceService)
    {
        $this->priceService = $priceService;
    }

    /**
     * Get the latest price for a given symbol and exchange.
     *
     * @param  string  $symbol
     * @return JsonResponse
     */
    public function getPrice(Request $request, $symbol)
    {
        $exchange_id = $request->query('exchange_id');

        // Determine Asset Type
        // exchanges_id: 1 = Forex, 2 = Crypto, 3 = Indices, 4 = Commodities, 5 = Stocks
        $assetType = 'crypto';
        if ($exchange_id == 1) {
            $assetType = 'forex';
        }
        if ($exchange_id == 5) {
            $assetType = 'stock';
        }

        // Symbols containing '/', 'USDT', 'BTC', 'ETH' are typically crypto if not specified
        if ($assetType == 'crypto' && ! ($exchange_id == 1 || $exchange_id == 5)) {
            $isCrypto = (str_contains($symbol, 'USDT') || str_contains($symbol, 'BTC') || str_contains($symbol, 'ETH') || str_contains($symbol, 'LTC'));
            if (! $isCrypto) {
                $assetType = 'forex';
            } // Default fallback
        }

        $priceData = $this->priceService->getPrice($symbol, $assetType);

        if ($priceData) {
            return response()->json($priceData);
        }

        return response()->json(['error' => 'Unable to fetch price from any provider'], 503);
    }
}
