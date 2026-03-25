<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class AdCategory extends Model
{
    use HasUuid;

    protected $fillable = [
        'name', 'slug', 'type', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function ads() { return $this->hasMany(Ad::class); }
}