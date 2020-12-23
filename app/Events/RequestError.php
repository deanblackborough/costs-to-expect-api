<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestError
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request_error = [];

    /**
     * Create a new event instance.
     *
     * @param array $request_error
     *
     * @return void
     */
    public function __construct(array $request_error)
    {
        $this->request_error = $request_error;
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
