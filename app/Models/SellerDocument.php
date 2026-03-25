<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SellerDocument extends Model
{
    use HasUuid;

    protected $fillable = [
        'seller_id',
        'document_type',
        'document_url',
        'cloudinary_public_id',
        'original_filename',
        'status',
        'rejection_reason',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}