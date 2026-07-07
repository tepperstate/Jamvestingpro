<?php

namespace App\Classes;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Activity
{
    public function insert($data, $id)
    {

        DB::table('activities')->insert([
            'user_id' => $id,
            'activities' => $data,
            'created_at' => Carbon::now(),
        ]);

        return true;
    }
}
