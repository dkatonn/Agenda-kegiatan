<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BirthdayToday extends Model
{
    use HasFactory;

    protected $fillable = [
        'birthday_date',
        'display_name',
        'source_payload',
        'fetched_at',
    ];

    protected function casts(): array
    {
        return [
            'birthday_date' => 'date',
            'source_payload' => 'array',
            'fetched_at' => 'datetime',
        ];
    }
}
