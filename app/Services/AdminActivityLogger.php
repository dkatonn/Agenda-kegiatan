<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminActivityLogger
{
    public function log(string $action, array $context = []): void
    {
        $user = Auth::user();

        Log::info($action, array_merge([
            'admin_id' => $user?->id,
            'admin_name' => $user?->name,
            'admin_nip' => $user?->nip,
            'ip' => request()?->ip(),
            'url' => request()?->fullUrl(),
        ], $context));
    }
}
