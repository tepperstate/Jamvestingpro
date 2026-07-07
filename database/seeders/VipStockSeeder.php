<?php

namespace Database\Seeders;

use App\Models\Stock_Trade;
use Illuminate\Database\Seeder;

class VipStockSeeder extends Seeder
{
    public function run()
    {
        $stocks = [
            ['name' => 'Berkshire Hathaway Inc. (Class A)', 'symbol' => 'BRK.A', 'buy' => 736150, 'sell' => 735000, 'volume' => 3000, 'changes' => 1],
            ['name' => 'Lindt & Sprüngli AG', 'symbol' => 'LISN.SW', 'buy' => 136840, 'sell' => 136000, 'volume' => 1000, 'changes' => 1],
            ['name' => 'NVR Inc.', 'symbol' => 'NVR', 'buy' => 9774, 'sell' => 9700, 'volume' => 10000, 'changes' => 1],
            ['name' => 'Booking Holdings Inc.', 'symbol' => 'BKNG', 'buy' => 5632, 'sell' => 5600, 'volume' => 25000, 'changes' => 1],
            ['name' => 'AutoZone Inc.', 'symbol' => 'AZO', 'buy' => 3821, 'sell' => 3800, 'volume' => 15000, 'changes' => 0],
            ['name' => 'Seaboard Corporation', 'symbol' => 'SEB', 'buy' => 3128, 'sell' => 3100, 'volume' => 5000, 'changes' => 0],
            ['name' => 'MercadoLibre Inc.', 'symbol' => 'MELI', 'buy' => 2363, 'sell' => 2350, 'volume' => 100000, 'changes' => 3],
            ['name' => 'First Citizens BancShares Inc.', 'symbol' => 'FCNCA', 'buy' => 2096, 'sell' => 2080, 'volume' => 30000, 'changes' => 1],
            ['name' => 'Fair Isaac Corporation', 'symbol' => 'FICO', 'buy' => 1775, 'sell' => 1760, 'volume' => 20000, 'changes' => 2],
            ['name' => 'White Mountains Insurance Group', 'symbol' => 'WTM', 'buy' => 1819, 'sell' => 1800, 'volume' => 8000, 'changes' => 0],
            ['name' => 'Markel Group Inc.', 'symbol' => 'MKL', 'buy' => 1594, 'sell' => 1580, 'volume' => 12000, 'changes' => 0],
            ['name' => 'MRF Ltd.', 'symbol' => 'MRF.NS', 'buy' => 1553, 'sell' => 1540, 'volume' => 5000, 'changes' => 1],
            ['name' => 'Mettler-Toledo International Inc.', 'symbol' => 'MTD', 'buy' => 1364, 'sell' => 1350, 'volume' => 18000, 'changes' => -1],
            ['name' => 'Coca-Cola Consolidated Inc.', 'symbol' => 'COKE', 'buy' => 1306, 'sell' => 1300, 'volume' => 22000, 'changes' => 0],
            ['name' => 'Kweichow Moutai Co. Ltd.', 'symbol' => '600519.SH', 'buy' => 270, 'sell' => 268, 'volume' => 500000, 'changes' => 1],
            ['name' => 'ASML Holding N.V.', 'symbol' => 'ASML', 'buy' => 1026, 'sell' => 1020, 'volume' => 80000, 'changes' => 2],
            ['name' => 'Hermès International SCA', 'symbol' => 'RMS.PA', 'buy' => 2450, 'sell' => 2440, 'volume' => 2000, 'changes' => 1],
            ['name' => 'Adyen N.V.', 'symbol' => 'ADYEN.AS', 'buy' => 1620, 'sell' => 1610, 'volume' => 5000, 'changes' => 2],
            ['name' => 'Givaudan SA', 'symbol' => 'GIVN.SW', 'buy' => 4850, 'sell' => 4830, 'volume' => 1000, 'changes' => 0],
            ['name' => 'Partners Group Holding AG', 'symbol' => 'PGHN.SW', 'buy' => 1420, 'sell' => 1410, 'volume' => 3000, 'changes' => 0],
            ['name' => 'TransDigm Group Inc.', 'symbol' => 'TDG', 'buy' => 1320, 'sell' => 1310, 'volume' => 25000, 'changes' => 1],
            ['name' => 'O\'Reilly Automotive Inc.', 'symbol' => 'ORLY', 'buy' => 1150, 'sell' => 1140, 'volume' => 30000, 'changes' => 1],
            ['name' => 'Deckers Outdoor Corp.', 'symbol' => 'DECK', 'buy' => 1050, 'sell' => 1040, 'volume' => 15000, 'changes' => 1],
            ['name' => 'Lam Research Corp.', 'symbol' => 'LRCX', 'buy' => 1010, 'sell' => 1000, 'volume' => 40000, 'changes' => 2],
            ['name' => 'W.W. Grainger Inc.', 'symbol' => 'GWW', 'buy' => 980, 'sell' => 970, 'volume' => 20000, 'changes' => 1],
            ['name' => 'Texas Pacific Land Corp.', 'symbol' => 'TPL', 'buy' => 850, 'sell' => 840, 'volume' => 10000, 'changes' => 1],
            ['name' => 'Broadcom Inc.', 'symbol' => 'AVGO', 'buy' => 1750, 'sell' => 1740, 'volume' => 150000, 'changes' => 2],
            ['name' => 'BYD Company Limited', 'symbol' => '002594.SZ', 'buy' => 290, 'sell' => 285, 'volume' => 2000000, 'changes' => 1],
            ['name' => 'Luzhou Laojiao Co. Ltd.', 'symbol' => '000568.SZ', 'buy' => 180, 'sell' => 178, 'volume' => 800000, 'changes' => 1],
            ['name' => 'Shanxi Xinghuacun Fen Wine', 'symbol' => '600809.SH', 'buy' => 230, 'sell' => 225, 'volume' => 600000, 'changes' => 1],
            ['name' => 'CATL (Contemporary Amperex)', 'symbol' => '300750.SZ', 'buy' => 195, 'sell' => 192, 'volume' => 3000000, 'changes' => 2],
            ['name' => 'Wuliangye Yibin Co. Ltd.', 'symbol' => '000858.SZ', 'buy' => 150, 'sell' => 148, 'volume' => 4000000, 'changes' => 1],
            ['name' => 'Foshan Haitian Flavoring', 'symbol' => '603288.SH', 'buy' => 45, 'sell' => 44, 'volume' => 5000000, 'changes' => 1],
            ['name' => 'Ping An Insurance Group', 'symbol' => '601318.SH', 'buy' => 55, 'sell' => 54, 'volume' => 10000000, 'changes' => 1],
            ['name' => 'China Merchants Bank', 'symbol' => '600036.SH', 'buy' => 35, 'sell' => 34, 'volume' => 15000000, 'changes' => 0],
            ['name' => 'Industrial and Commercial Bank', 'symbol' => '601398.SH', 'buy' => 6, 'sell' => 5, 'volume' => 50000000, 'changes' => 0],
            ['name' => 'Tencent Holdings Ltd.', 'symbol' => '0700.HK', 'buy' => 400, 'sell' => 398, 'volume' => 20000000, 'changes' => 2],
            ['name' => 'Alibaba Group Holding Ltd.', 'symbol' => '9988.HK', 'buy' => 85, 'sell' => 84, 'volume' => 30000000, 'changes' => 1],
            ['name' => 'Meituan', 'symbol' => '3690.HK', 'buy' => 120, 'sell' => 118, 'volume' => 15000000, 'changes' => 2],
            ['name' => 'NetEase Inc.', 'symbol' => '9999.HK', 'buy' => 150, 'sell' => 148, 'volume' => 5000000, 'changes' => 1],
        ];

        foreach ($stocks as $item) {
            Stock_Trade::updateOrCreate(
                ['symbol' => $item['symbol']],
                array_merge($item, ['is_vip' => true, 'image' => 'default_stock.png'])
            );
        }
    }
}
