<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TvContentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $revision)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('tv.display'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'tv.content.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'revision' => $this->revision,
        ];
    }
}
