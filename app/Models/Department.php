<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'external_id',
        'name',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class)->orderBy('name');
    }
}
