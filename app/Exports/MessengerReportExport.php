<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MessengerReportExport implements FromCollection, WithHeadings
{
    public function __construct(
        private Collection $rows
    ) {
    }

    public function headings(): array
    {
        return ['fecha', 'mensajero', 'envio', 'cliente', 'estado', 'valor', 'estado_pago'];
    }

    public function collection(): Collection
    {
        return $this->rows;
    }
}
