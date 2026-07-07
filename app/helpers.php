<?php

use App\Models\Site_setting;
use Illuminate\Support\Facades\DB;

if (! function_exists('site')) {
    function site()
    {
        return cache()->remember('site_setting', 3600, function () {
            return Site_setting::whereId(1)->first() ?? new Site_setting([
                'name' => 'Platform',
                'logo' => 'logo.png',
                'favicon' => 'favicon.png',
            ]);
        });
    }
}

if (! function_exists('count_email')) {
    function count_email()
    {
        $user = user();

        return DB::table('emails')->whereUserId($user)->where(['sent_to' => 'inbox', 'status' => 'unread'])->count();
    }
}

if (! function_exists('unread')) {
    function unread()
    {
        $user = user();

        return DB::table('emails')->whereUserId($user)->where(['sent_to' => 'inbox'])->get();
    }
}

if (! function_exists('unread_count')) {
    function unread_count()
    {
        $user = user();

        return DB::table('emails')->whereUserId($user)->where(['sent_to' => 'inbox', 'status' => 'unread'])->count();
    }
}

if (! function_exists('count_noti')) {
    function count_noti()
    {
        $user = user();

        return DB::table('notis')->whereUserId($user)->where(['status' => 'unread'])->count();
    }
}
if (! function_exists('not')) {
    function not()
    {
        $user = user();

        return DB::table('notis')->whereUserId($user)->where(['status' => 'unread'])->get();
    }
}

if (! function_exists('user')) {
    function user()
    {
        return auth()->guard('web')->user()->id ?? null;
    }
}

if (! function_exists('user_bots')) {
    function user_bots()
    {
        $user = user();

        return DB::table('purchase_bot')->whereUserId($user)->get();
    }
}

if (! function_exists('code')) {
    function code()
    {
        return cache()->remember('chat_code', 3600, function () {
            return DB::table('chats')->first();
        });
    }
}
