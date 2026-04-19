<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Shipments\ShipmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentStatusHistory extends Model
{
    use BelongsToOrganization;

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'from_status',
        'to_status',
        'notes',
        'changed_by_user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (ShipmentStatusHistory $history): void {
            if ($history->created_at === null) {
                $history->created_at = now();
            }

            if ($history->organization_id !== null) {
                return;
            }

            $shipment = Shipment::withoutGlobalScopes()->find($history->shipment_id);

            if ($shipment !== null) {
                $history->organization_id = $shipment->organization_id;
            }
        });
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    public function fromStatusLabel(): ?string
    {
        return $this->from_status !== null
            ? ShipmentStatus::label($this->from_status)
            : null;
    }

    public function toStatusLabel(): string
    {
        return ShipmentStatus::label($this->to_status);
    }
}
