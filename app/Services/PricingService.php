<?php

namespace App\Services;

use App\Models\PricingRule;

class PricingService
{
    public function calculate(
        string $serviceType,
        string $format,
        string $colorMode,
        int $quantity,
        int $pages = 1,
        ?int $printerId = null
    ): array {
        $rule = PricingRule::query()
            ->where('is_active', true)
            ->where('service_type', $serviceType)
            ->where('format', $format)
            ->where('color_mode', $colorMode)
            ->when($printerId, fn ($q) => $q->where('printer_id', $printerId))
            ->when(! $printerId, fn ($q) => $q->whereNull('printer_id'))
            ->first();

        if (! $rule) {
            $rule = PricingRule::query()
                ->where('is_active', true)
                ->whereNull('printer_id')
                ->where('service_type', $serviceType)
                ->where('format', $format)
                ->where('color_mode', $colorMode)
                ->first();
        }

        if (! $rule) {
            $defaults = [
                'print' => ['bw' => 50, 'color' => 150],
                'photocopy' => ['bw' => 30, 'color' => 100],
                'scan' => ['bw' => 100, 'color' => 100],
            ];
            $unitPrice = $defaults[$serviceType][$colorMode] ?? 50;
            $setupFee = 0;
        } else {
            $unitPrice = (float) $rule->price_per_unit;
            $setupFee = (float) $rule->setup_fee;
        }

        $units = $serviceType === 'scan' ? $pages : $quantity * $pages;
        $subtotal = $unitPrice * $units;
        $total = $subtotal + $setupFee;

        return [
            'unit_price' => round($unitPrice, 2),
            'setup_fee' => round($setupFee, 2),
            'units' => $units,
            'total_price' => round($total, 2),
        ];
    }
}
