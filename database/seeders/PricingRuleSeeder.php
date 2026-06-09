<?php

namespace Database\Seeders;

use App\Models\PricingRule;
use App\Models\User;
use Illuminate\Database\Seeder;

class PricingRuleSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['print', 'A4', 'bw', 50, 200],
            ['print', 'A4', 'color', 150, 500],
            ['print', 'A3', 'bw', 100, 400],
            ['print', 'A3', 'color', 300, 800],
            ['photocopy', 'A4', 'bw', 30, 100],
            ['photocopy', 'A4', 'color', 100, 300],
            ['scan', 'A4', 'bw', 100, 0],
            ['scan', 'A4', 'color', 100, 0],
        ];

        foreach ($defaults as [$service, $format, $color, $price, $setup]) {
            PricingRule::create([
                'printer_id' => null,
                'service_type' => $service,
                'format' => $format,
                'color_mode' => $color,
                'price_per_unit' => $price,
                'setup_fee' => $setup,
                'is_active' => true,
            ]);
        }

        $printers = User::where('role', 'printer')->get();
        foreach ($printers as $printer) {
            PricingRule::create([
                'printer_id' => $printer->id,
                'service_type' => 'print',
                'format' => 'A4',
                'color_mode' => 'bw',
                'price_per_unit' => 45,
                'setup_fee' => 150,
                'is_active' => true,
            ]);
        }
    }
}
