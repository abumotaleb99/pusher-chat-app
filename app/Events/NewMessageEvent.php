<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    public $sender_id;
    public $receiver_id;
    public $time;

    public function __construct($message, $sender_id, $receiver_id)
    {
        $this->message      = $message;
        $this->sender_id    = $sender_id;
        $this->receiver_id  = $receiver_id;
        $this->time         = now()->timezone('Asia/Dhaka')->format('h:i A');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        \Log::info('Broadcasting to: my-channel.' . $this->receiver_id);
        return new Channel('my-channel.' . $this->receiver_id);
    }

    public function broadcastAs()
    {
        return 'my-event';
    }


}
