<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $order;

    


    public function __construct(Order $order)
    {
        $this->order = [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total' => $order->total,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
        ];
    }

    




    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin.orders'),
        ];
    }
    
    


    public function broadcastAs(): string
    {
        return 'NewOrderPlaced';
    }
}
