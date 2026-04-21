<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FinancialMovementsExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Collection $rows
    ) {
    }

    public function headings(): array
    {
        return ['fecha', 'cliente', 'envio', 'valor', 'estado', 'usuario', 'tipo'];
    }

    public function collection(): Collection
    {
        return $this->rows->map(function (array $row): array {
            return [
                'fecha' => optional($row['date'])->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
                'cliente' => $row['customer'],
                'envio' => $row['tracking'],
                'valor' => $row['value'],
                'estado' => $row['status'],
                'usuario' => $row['user'],
                'tipo' => $row['type'],
            ];
        });
    }
}
