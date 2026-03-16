<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'title',
        'agenda_date',
        'agenda_time',
        'location',
        'disposition',
        'description',
        'unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'agenda_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
