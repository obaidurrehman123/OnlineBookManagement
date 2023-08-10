<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\UpdateOrderTotal;

class RecalculateOrderTotal
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UpdateOrderTotal $event): void
    {
        $order = $event->order;
        $newTotal = $order->orderItems->sum('subtotal');
        dd($newTotal);
        $order->update(['total_amount' => $newTotal]);
    }
}
