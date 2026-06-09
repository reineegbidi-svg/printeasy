<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    public function generateReference(): string
    {
        do {
            $ref = 'PE-'.strtoupper(Str::random(8));
        } while (Order::where('reference', $ref)->exists());

        return $ref;
    }

    public function updateStatus(Order $order, string $newStatus, ?User $actor = null, ?string $comment = null): Order
    {
        $from = $order->status;
        $order->update([
            'status' => $newStatus,
            'completed_at' => in_array($newStatus, ['completed', 'delivered'], true)
                ? ($order->completed_at ?? now())
                : $order->completed_at,
            'rejection_reason' => $newStatus === 'rejected' ? ($comment ?? $order->rejection_reason) : $order->rejection_reason,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $from,
            'to_status' => $newStatus,
            'changed_by' => $actor?->id,
            'comment' => $comment,
        ]);

        $this->notifications->notifyOrderStatus($order->fresh(['user', 'printer']), $from, $newStatus, $actor);

        return $order->fresh();
    }
}
