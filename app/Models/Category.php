<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Category extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'slug', 'icon', 'image',
        'commission_rate', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}