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

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
