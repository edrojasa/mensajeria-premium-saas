<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentSequence extends Model
{
    protected $fillable = [
        'organization_id',
        'year',
        'next_sequence',
    ];

    protected $casts = [
        'year' => 'integer',
        'next_sequence' => 'integer',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
