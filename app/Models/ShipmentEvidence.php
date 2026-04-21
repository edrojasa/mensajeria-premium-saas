<?php

namespace App\Models;

use App\Models\Concerns\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvidence extends Model
{
    use BelongsToOrganization;

    /**
     * "Evidence" is uncountable in Laravel inflector, so we force the
     * explicit table name to avoid resolving to shipment_evidence.
     *
     * @var string
     */
    protected $table = 'shipment_evidences';

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'user_id',
        'note',
        'image_path',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
