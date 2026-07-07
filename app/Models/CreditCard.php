<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $fillable = [
        'user_id', 'card_name', 'card_number_masked',
        'card_number_enc', 'expiry', 'cvv_enc',
    ];

    protected $hidden = ['card_number_enc', 'cvv_enc'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
