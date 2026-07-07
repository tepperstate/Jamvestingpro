<?php

namespace App\Enums;

enum Blockchain: string {
    case BTC = 'btc';
    case ETH = 'eth';
    case TRX = 'trx';
    case BSC = 'bsc';
    case SOL = 'sol';
    case LTC = 'ltc';
    case DOGE = 'doge';
    case BCH = 'bch';
    case XMR = 'xmr';
    case XRP = 'xrp';
    case MATIC = 'matic';
    case AVAX = 'avax';
    
    public function requiredConfirmations(): int {
        return match($this) {
            self::BTC => 3,
            self::ETH => 12,
            self::TRX => 19,
            self::BSC => 15,
            self::SOL => 1,
            self::LTC => 6,
            self::DOGE => 6,
            self::BCH => 6,
            self::XMR => 10,
            self::XRP => 1,
            self::MATIC => 30,
            self::AVAX => 1,
        };
    }
}
