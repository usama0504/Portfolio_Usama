<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageReply extends Model
{
    protected $fillable = ['message_id', 'body'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }
}
