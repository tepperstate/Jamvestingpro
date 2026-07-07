<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class P2pChatMessage extends Model
{
    protected $fillable = [
        'p2p_order_id',
        'sender_id',
        'message',
    ];

    public function order()
    {
        return $this->belongsTo(P2pOrder::class, 'p2p_order_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
