<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * Get all of the users for the company.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all of the service types for the company.
     */
    public function serviceTypes(): HasMany
    {
        return $this->hasMany(ServiceType::class);
    }

    /**
     * Get all of the call reports for the company.
     */
    public function callReports(): HasMany
    {
        return $this->hasMany(CallReport::class);
    }
}
