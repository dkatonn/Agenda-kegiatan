<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Agenda extends Model
{
    use HasFactory, LogsActivity;

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
        'created_by',
        'updated_by',
        'locked_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('agenda')
            ->logOnly([
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
                'created_by',
                'updated_by',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return $eventName;
    }
}
