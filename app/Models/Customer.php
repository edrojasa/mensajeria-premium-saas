<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToOrganization;
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'name',
        'document',
        'phone',
        'email',
        'notes',
    ];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer): void {
            if ($customer->customer_code !== null && $customer->customer_code !== '') {
                return;
            }

            $orgId = $customer->organization_id ?? tenant_id();
            if ($orgId === null) {
                return;
            }

            $customer->customer_code = app(\App\Support\CustomerCodeGenerator::class)
                ->generate((int) $orgId);
        });
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
