<?php

namespace App\Finance;

use App\Models\ServiceRate;

final class ShipmentCostCalculator
{
    public function calculate(
        ?ServiceRate $rate,
        mixed $weightKg,
        mixed $distanceKm
    ): ?float {
        if ($rate === null || ! $rate->active) {
            return null;
        }

        $base = (float) $rate->base_price;
        $w = $weightKg !== null && $weightKg !== '' ? (float) $weightKg : 0.0;
        $d = $distanceKm !== null && $distanceKm !== '' ? (float) $distanceKm : 0.0;

        $addW = $rate->price_per_kg !== null ? $w * (float) $rate->price_per_kg : 0.0;
        $addD = $rate->price_per_km !== null ? $d * (float) $rate->price_per_km : 0.0;

        return round($base + $addW + $addD, 2);
    }
}
