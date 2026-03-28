<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class SellerBadge extends Model {
    use HasUuid;
    protected $table = 'seller_badges';
    protected $fillable = [
        'name','slug','icon','color','description',
        'criteria_type','criteria_value','is_active',
    ];
    protected $casts = ['is_active' => 'boolean'];

    public function sellers() {
        return $this->belongsToMany(Seller::class, 'seller_badge_pivot', 'badge_id', 'seller_id')
            ->withPivot('awarded_at')->withTimestamps();
    }
}