<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'lastname',
        'email',
    ];

    /**
     * The companies that this customer has interacted with.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'customer_company');
    }

    /**
     * All call reports for this customer, across all companies.
     */
    public function callReports(): HasMany
    {
        return $this->hasMany(CallReport::class);
    }
}
