<?php

namespace App\Services;

use App\Models\PricingRule;
use App\Models\PrinterAvailability;
use App\Models\User;

class PrinterSetupService
{
    public function bootstrap(User $user): void
    {
        if (! $user->isPrinter()) {
            return;
        }

        foreach (range(1, 5) as $day) {
            PrinterAvailability::firstOrCreate(
                ['printer_id' => $user->id, 'day_of_week' => $day],
                [
                    'start_time' => '08:00',
                    'end_time' => '18:00',
                    'is_available' => true,
                ]
            );
        }

        $defaults = [
            ['print', 'A4', 'bw', 50, 200],
            ['print', 'A4', 'color', 150, 500],
            ['photocopy', 'A4', 'bw', 30, 100],
            ['scan', 'A4', 'bw', 100, 0],
        ];

        foreach ($defaults as [$service, $format, $color, $price, $setup]) {
            PricingRule::firstOrCreate(
                [
                    'printer_id' => $user->id,
                    'service_type' => $service,
                    'format' => $format,
                    'color_mode' => $color,
                ],
                [
                    'price_per_unit' => $price,
                    'setup_fee' => $setup,
                    'is_active' => true,
                ]
            );
        }

        $user->update(['is_available' => true]);
    }
}
