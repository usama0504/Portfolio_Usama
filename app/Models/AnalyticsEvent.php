<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['type', 'label', 'visitor', 'device', 'referrer', 'created_at'];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }
}
