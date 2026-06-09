<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderAssignmentService
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    public function accept(Order $order, User $printer): Order
    {
        $this->ensurePrinterCanAccept($printer);

        return DB::transaction(function () use ($order, $printer) {
            $locked = Order::lockForUpdate()->find($order->id);

            if (! $locked || $locked->status !== 'pending' || $locked->printer_id !== null) {
                throw new ConflictHttpException(
                    'Cette commande a déjà été acceptée par un autre imprimeur.'
                );
            }

            $locked->update([
                'printer_id' => $printer->id,
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            OrderStatusHistory::create([
                'order_id' => $locked->id,
                'from_status' => 'pending',
                'to_status' => 'accepted',
                'changed_by' => $printer->id,
                'comment' => 'Commande acceptée par l\'imprimeur',
            ]);

            $fresh = $locked->fresh(['user', 'printer']);

            $this->notifications->notifyOrderAccepted($fresh, $printer);

            return $fresh;
        });
    }

    protected function ensurePrinterCanAccept(User $printer): void
    {
        if (! $printer->isPrinter()) {
            throw new HttpException(403, 'Accès réservé aux imprimeurs.');
        }

        if (! $printer->is_active) {
            throw new HttpException(403, 'Votre compte est désactivé.');
        }

        if (! $printer->is_approved) {
            throw new HttpException(403, 'Votre compte imprimeur est en attente de validation par un administrateur.');
        }

        if (! $printer->is_available) {
            throw new HttpException(422, 'Activez votre disponibilité pour accepter des commandes.');
        }
    }
}
