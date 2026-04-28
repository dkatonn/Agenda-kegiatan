<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Video extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
        'file_path',
        'source_type',
        'source_path',
        'unit',
        'is_active',
        'sort_order',
        'display_order',
        'created_by',
        'updated_by',
        'locked_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'locked_at' => 'datetime',
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
            ->useLogName('video')
            ->logOnly([
                'title',
                'file_path',
                'source_type',
                'source_path',
                'unit',
                'is_active',
                'sort_order',
                'display_order',
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
