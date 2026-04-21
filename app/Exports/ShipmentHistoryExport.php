<?php

namespace App\Exports;

use App\Models\Shipment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShipmentHistoryExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Shipment $shipment
    ) {
    }

    public function headings(): array
    {
        return [
            'tracking_number',
            'type',
            'status_from',
            'status_to',
            'note',
            'user',
            'created_at',
            'image_url',
        ];
    }

    public function collection(): Collection
    {
        $statusRows = $this->shipment->statusHistories->map(function ($row): array {
            return [
                'tracking_number' => $this->shipment->tracking_number,
                'type' => 'status',
                'status_from' => $row->from_status ?? '',
                'status_to' => $row->to_status ?? '',
                'note' => $row->notes ?? '',
                'user' => $row->changedBy?->name ?? '',
                'created_at' => optional($row->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'image_url' => '',
            ];
        });

        $evidenceRows = $this->shipment->evidences->map(function ($row): array {
            return [
                'tracking_number' => $this->shipment->tracking_number,
                'type' => 'evidence',
                'status_from' => '',
                'status_to' => '',
                'note' => $row->note ?? '',
                'user' => $row->author?->name ?? '',
                'created_at' => optional($row->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'image_url' => $row->image_path ? asset('storage/'.$row->image_path) : '',
            ];
        });

        return $statusRows
            ->concat($evidenceRows)
            ->sortBy('created_at')
            ->values();
    }
}
