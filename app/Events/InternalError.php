<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InternalError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $internal_error = [];

    /**
     * Create a new event instance.
     *
     * @param array $internal_error
     *
     * @return void
     */
    public function __construct(array $internal_error)
    {
        $this->internal_error = $internal_error;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
