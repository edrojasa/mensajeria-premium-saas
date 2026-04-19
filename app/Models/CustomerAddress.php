<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'label',
        'address_line',
        'city',
        'department',
        'department_id',
        'city_id',
        'reference_notes',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function departmentModel(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function cityModel(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
