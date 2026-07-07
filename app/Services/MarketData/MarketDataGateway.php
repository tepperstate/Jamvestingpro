<?php

namespace App\Services\MarketData;

class MarketDataGateway
{
    /**
     * Get the latest price for a symbol, routing to the appropriate market data service.
     */
    public function getPrice($symbol, $assetType = 'crypto')
    {
        switch ($assetType) {
            case 'stock':
                $service = new StockPriceService;
                break;
            case 'forex':
                $service = new ForexPriceService;
                break;
            case 'crypto':
            default:
                $service = new CryptoPriceService;
                break;
        }

        return $service->getPrice($symbol);
    }
}
