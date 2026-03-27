<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'name',
        'location',
        'disposition',
        'title',
        'agenda_date',
        'agenda_time',
        'description',
        'unit',
        'is_active',
    ];
}
