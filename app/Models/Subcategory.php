<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Subcategory extends Model
{
    use HasUuid;

    protected $fillable = [
        'category_id', 'name', 'slug', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function category() { return $this->belongsTo(Category::class); }
}