<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <-- ADD THIS

class Company extends Model
{
    protected $fillable = [
        'name'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function serviceTypes(): HasMany
    {
        return $this->hasMany(ServiceType::class);
    }

    /**
     * All call reports this company has received.
     */
    public function callReports(): HasMany
    {
        return $this->hasMany(CallReport::class);
    }

    /**
     * The customers that have interacted with this company.
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_company');
    }
}
