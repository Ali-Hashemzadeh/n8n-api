<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CallReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These are the "flattened" fields we'll use from the n8n data.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'customer_phone',
        'customer_name',
        'customer_lastname',
        'customer_email',
        'summary',
        'conversation',
        'metadata',
        'state',
        'created_at', // We include this so we can set it manually from n8n's data
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conversation' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the company that this call report belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
