<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site_setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'favicon',
        'meta',
        'phone',
        'email',
        'address',
        'video',
        'withdrawal_flow_enabled',
        'default_withdrawal_security',
        'clearance_pin_name',
        'tax_pin_name',
        'liquidation_pin_name',
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_pass',
        'smtp_encryption',
        'mail_from_address',
        'app_debug',
        'app_url',
        'withdrawal_message',
        'google_client_id',
        'google_client_secret',
        'google_redirect_url',
        'spot_auto_approve',
        'spot_auto_win_percent',
        'margin_auto_approve',
        'margin_auto_win_percent',
        'futures_auto_approve',
        'futures_auto_win_percent',
        'pusher_app_id',
        'pusher_app_key',
        'pusher_app_secret',
        'pusher_app_cluster',
        'alphavantage_api_key',
        'finnhub_api_key',
        'binance_api_key',
        'binance_api_secret',
        'coingecko_api_key',
        'use_round_robin',
        'twelve_data_api_key',
        'polygon_api_key',
        'auto_sync_logos',
        'maker_fee',
        'taker_fee',
        'withdrawal_fee',
    ];
}
