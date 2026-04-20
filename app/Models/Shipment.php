<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use App\Finance\PaymentStatus;
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
        'customer_id',
        'assigned_user_id',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
        'declared_value' => 'decimal:2',
        'distance_km' => 'decimal:3',
        'cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function balanceDue(): float
    {
        if ($this->payment_status === PaymentStatus::PAID) {
            return 0.0;
        }

        $cost = (float) ($this->cost ?? 0);
        $paid = (float) ($this->paid_amount ?? 0);

        return max(0.0, round($cost - $paid, 2));
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedCourier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
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
