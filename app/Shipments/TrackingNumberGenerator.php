<?php

namespace App\Shipments;

use App\Models\ShipmentSequence;
use Illuminate\Support\Facades\DB;

/**
 * Genera números RT-YYYY-XXXXXX únicos por organización y año, correlativos.
 */
class TrackingNumberGenerator
{
    private const PREFIX = 'RT';

    /**
     * Formato: RT-YYYY-XXXXXX (6 dígitos con ceros a la izquierda).
     */
    public function generate(int $organizationId): string
    {
        $year = (int) now()->year;

        return DB::transaction(function () use ($organizationId, $year): string {
            $sequence = ShipmentSequence::query()
                ->where('organization_id', $organizationId)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            if ($sequence === null) {
                ShipmentSequence::create([
                    'organization_id' => $organizationId,
                    'year' => $year,
                    'next_sequence' => 2,
                ]);

                $assigned = 1;
            } else {
                $assigned = $sequence->next_sequence;
                $sequence->update([
                    'next_sequence' => $sequence->next_sequence + 1,
                ]);
            }

            return sprintf('%s-%d-%06d', self::PREFIX, $year, $assigned);
        });
    }
}
