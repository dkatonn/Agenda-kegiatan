<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'source_type',
        'source_path',
        'unit',
        'is_active',
        'sort_order',
        'display_order',
    ];
}
