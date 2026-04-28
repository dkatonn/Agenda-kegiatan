<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class EditLockService
{
    public function expirationMinutes(): int
    {
        return 10;
    }

    public function acquire(Model $model, Authenticatable $user): array
    {
        $model = $this->refreshExpiredLock($model);
        $lockedBy = $this->lockedBy($model);

        if ($lockedBy !== null && $lockedBy !== (int) $user->getAuthIdentifier()) {
            return [
                'acquired' => false,
                'lock' => $this->payload($model, (int) $user->getAuthIdentifier()),
            ];
        }

        if ($this->supportsLocking($model)) {
            $model->newQuery()
                ->whereKey($model->getKey())
                ->update([
                    'locked_by' => $user->getAuthIdentifier(),
                    'locked_at' => now(),
                ]);
        }

        return [
            'acquired' => true,
            'lock' => $this->payload($model->fresh(), (int) $user->getAuthIdentifier()),
        ];
    }

    public function release(Model $model, ?Authenticatable $user = null, bool $force = false): void
    {
        if (! $this->supportsLocking($model)) {
            return;
        }

        $query = $model->newQuery()->whereKey($model->getKey());

        if (! $force && $user !== null) {
            $query->where('locked_by', $user->getAuthIdentifier());
        }

        $query->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    public function isLockedByAnother(Model $model, ?int $userId): bool
    {
        $model = $this->refreshExpiredLock($model);
        $lockedBy = $this->lockedBy($model);

        return $lockedBy !== null && $lockedBy !== $userId;
    }

    public function payload(Model $model, ?int $viewerId = null): array
    {
        $model = $this->refreshExpiredLock($model);
        $lockedAt = $this->lockTimestamp($model);
        $expiresAt = $lockedAt?->copy()->addMinutes($this->expirationMinutes());

        if (method_exists($model, 'locker')) {
            $model->loadMissing('locker');
        }

        if (method_exists($model, 'updater')) {
            $model->loadMissing('updater');
        }

        $lockedBy = $this->lockedBy($model);
        $updatedAt = $model->updated_at ? Carbon::parse($model->updated_at) : null;

        return [
            'is_locked' => $lockedBy !== null,
            'is_mine' => $lockedBy !== null && $lockedBy === $viewerId,
            'locked_by_id' => $lockedBy,
            'locked_by_name' => $model->locker?->name,
            'locked_at' => $lockedAt?->toIso8601String(),
            'locked_at_label' => $this->formatTimestamp($lockedAt),
            'expires_at' => $expiresAt?->toIso8601String(),
            'expires_at_label' => $this->formatTimestamp($expiresAt),
            'updated_at' => $updatedAt?->toIso8601String(),
            'updated_at_label' => $this->formatTimestamp($updatedAt),
            'updated_by_id' => $model->updated_by,
            'updated_by_name' => $model->updater?->name,
        ];
    }

    public function lockMessage(Model $model): string
    {
        $payload = $this->payload($model);
        $lockerName = $payload['locked_by_name'] ?: 'admin lain';

        return "Data sedang diedit oleh {$lockerName}.";
    }

    public function supportsLocking(Model $model): bool
    {
        $table = $model->getTable();

        return Schema::hasColumn($table, 'locked_by') && Schema::hasColumn($table, 'locked_at');
    }

    protected function refreshExpiredLock(Model $model): Model
    {
        if (! $this->supportsLocking($model)) {
            return $model;
        }

        $lockedAt = $this->lockTimestamp($model);
        $lockedBy = $this->lockedBy($model);

        if ($lockedBy === null || $lockedAt === null) {
            return $model;
        }

        if ($lockedAt->addMinutes($this->expirationMinutes())->isFuture()) {
            return $model;
        }

        $model->newQuery()
            ->whereKey($model->getKey())
            ->update([
                'locked_by' => null,
                'locked_at' => null,
            ]);

        return $model->fresh() ?? $model;
    }

    protected function lockTimestamp(Model $model): ?Carbon
    {
        if (! $model->getAttribute('locked_at')) {
            return null;
        }

        return Carbon::parse($model->getAttribute('locked_at'));
    }

    protected function lockedBy(Model $model): ?int
    {
        $lockedBy = $model->getAttribute('locked_by');

        return $lockedBy !== null ? (int) $lockedBy : null;
    }

    protected function formatTimestamp(?Carbon $timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }

        return $timestamp->timezone(config('app.timezone'))->locale('id')->translatedFormat('d F Y H:i');
    }
}
