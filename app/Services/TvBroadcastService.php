<?php

namespace App\Services;

use App\Events\TvContentUpdated;
use Illuminate\Support\Facades\Log;
use Throwable;

class TvBroadcastService
{
    public function dispatchUpdate(string $revision): void
    {
        try {
            TvContentUpdated::dispatch($revision);
        } catch (Throwable $exception) {
            Log::warning('Broadcast TV update gagal. Fallback ke mode non-WebSocket.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);
        }
    }
}
