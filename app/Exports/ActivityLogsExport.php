<?php

namespace App\Exports;

use App\Support\AuditActionPresenter;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityLogsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private Builder $query)
    {
    }

    public function query(): Builder
    {
        return $this->query->clone();
    }

    public function headings(): array
    {
        return [
            __('exports.logs_col_date'),
            __('exports.logs_col_user'),
            __('exports.logs_col_action'),
            __('exports.logs_col_description'),
        ];
    }

    /**
     * @param  \App\Models\ActivityLog  $log
     * @return array<int, string|null>
     */
    public function map($log): array
    {
        return [
            $log->created_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s') ?? '',
            $log->user?->name ?? '—',
            AuditActionPresenter::label($log->action),
            $log->description ?? '—',
        ];
    }
}
