<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MonnifyTransaction extends Model
{
    use HasUuids;

    protected $fillable = [
        'reference',
        'monnify_reference',
        'payable_type',
        'payable_id',
        'amount',
        'currency',
        'type',
        'status',
        'gateway_response',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
    ];

    /**
     * The owner of this transaction (User, Seller, etc.)
     */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}