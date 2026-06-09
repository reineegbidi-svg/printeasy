<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function notifyOrderStatus(Order $order, string $fromStatus, string $toStatus, ?User $actor = null): void
    {
        $labels = [
            'pending' => 'En attente',
            'accepted' => 'Acceptée',
            'in_progress' => 'En cours',
            'completed' => 'Terminée',
            'delivered' => 'Livrée',
            'rejected' => 'Refusée',
            'cancelled' => 'Annulée',
        ];

        $fromLabel = $labels[$fromStatus] ?? $fromStatus;
        $toLabel = $labels[$toStatus] ?? $toStatus;

        $title = sprintf('Commande %s - %s', $order->reference, $toLabel);
        $message = sprintf(
            'Votre commande est passée de « %s » à « %s ».',
            $fromLabel,
            $toLabel
        );

        $user = $order->user;
        if ($user === null) {
            return;
        }

        $this->create($user, 'order_status', $title, $message, $order);

        $printer = $order->printer;
        if ($printer !== null) {
            $this->create($printer, 'order_status', $title, $message, $order);
        }

        $this->sendEmail($user->email, $title, $message);
    }

    public function notifyOrderAccepted(Order $order, User $printer): void
    {
        $user = $order->user;
        if ($user === null) {
            return;
        }

        $title = sprintf('Commande %s acceptée', $order->reference);
        $message = sprintf(
            'Votre commande a été prise en charge par %s. Vous pouvez suivre son avancement dans votre espace.',
            $printer->name
        );

        $this->create($user, 'order_accepted', $title, $message, $order);
        $this->create($printer, 'order_accepted', $title, 'Vous avez accepté cette commande.', $order);
        $this->sendEmail($user->email, $title, $message);
    }

    public function create(User $user, string $type, string $title, string $message, ?Order $order = null, array $data = []): AppNotification
    {
        return AppNotification::create([
            'user_id' => $user->id,
            'order_id' => $order?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    protected function sendEmail(string $email, string $subject, string $body): void
    {
        try {
            Mail::raw($body, function ($message) use ($email, $subject): void {
                $message->to($email)->subject('[PrintEasy] '.$subject);
            });
        } catch (\Throwable $e) {
            Log::info('Email simulé pour '.$email.': '.$subject);
        }
    }
}
