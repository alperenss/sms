<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function smsDetail()
    {
        return $this->hasOne(SentMessageDetail::class, 'sent_message_id', 'id');
    }
}
