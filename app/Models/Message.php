<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Message extends Model
{
    protected $fillable = ['name', 'email', 'subject', 'body', 'ip', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function replies(): HasMany
    {
        return $this->hasMany(MessageReply::class)->oldest();
    }

    /** Unread messages; 0 if the table does not exist yet. */
    public static function unreadCount(): int
    {
        try {
            return static::query()->whereNull('read_at')->count();
        } catch (\Throwable) {
            return 0;
        }
    }
}
