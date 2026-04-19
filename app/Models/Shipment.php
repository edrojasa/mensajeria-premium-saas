<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Shipments\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shipment extends Model
{
    use BelongsToOrganization;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'tracking_number',
        'sender_name',
        'sender_phone',
        'sender_email',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'origin_address_line',
        'origin_city',
        'origin_region',
        'origin_postal_code',
        'destination_address_line',
        'destination_city',
        'destination_region',
        'destination_postal_code',
        'origin_department_id',
        'origin_city_id',
        'destination_department_id',
        'destination_city_id',
        'reference_internal',
        'notes_internal',
        'weight_kg',
        'declared_value',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
        'declared_value' => 'decimal:2',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class)->orderBy('created_at');
    }

    public function statusLabel(): string
    {
        return ShipmentStatus::label($this->status);
    }
}
