<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'to',
        'sent_to',
        'subject',
        'message',
        'status',
        'thread_id',
        'reply_to_id',
    ];
}
