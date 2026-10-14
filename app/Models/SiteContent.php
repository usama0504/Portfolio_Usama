<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    /**
     * All saved sections as [key => content]. Returns an empty array when the
     * table does not exist yet, so the public site keeps working (with its
     * built-in defaults) until `php artisan migrate` has been run.
     *
     * @return array<string, mixed>
     */
    public static function sections(): array
    {
        try {
            return static::query()
                ->get()
                ->mapWithKeys(fn (self $row) => [$row->key => $row->value])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
