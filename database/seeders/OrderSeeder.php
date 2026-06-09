<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@printeasy.com')->first();
        $printer = User::where('email', 'imprimeur@printeasy.com')->first();
        $orderService = app(OrderService::class);

        $statuses = ['pending', 'accepted', 'in_progress', 'completed', 'rejected'];

        foreach ($statuses as $i => $status) {
            $order = Order::create([
                'reference' => 'PE-DEMO'.str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'printer_id' => $printer->id,
                'service_type' => ['print', 'photocopy', 'scan'][$i % 3],
                'format' => 'A4',
                'color_mode' => $i % 2 === 0 ? 'bw' : 'color',
                'quantity' => rand(1, 50),
                'pages' => rand(1, 20),
                'unit_price' => 50,
                'total_price' => rand(500, 15000),
                'status' => $status,
                'payment_method' => $i % 2 === 0 ? 'online' : 'on_delivery',
                'payment_status' => $status === 'completed' ? 'paid' : 'unpaid',
                'file_name' => "document_{$i}.pdf",
                'file_path' => 'orders/demo.pdf',
                'file_type' => 'application/pdf',
                'completed_at' => $status === 'completed' ? now() : null,
            ]);

            if ($status === 'completed') {
                Payment::create([
                    'transaction_id' => 'TXN-DEMO'.strtoupper(Str::random(8)),
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'amount' => $order->total_price,
                    'method' => 'mobile_money',
                    'status' => 'completed',
                    'provider_reference' => 'MM-DEMO123',
                    'paid_at' => now(),
                ]);
            }
        }
    }
}
