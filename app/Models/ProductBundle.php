<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class ProductBundle extends Model {
    use HasUuid;
    protected $table = 'product_bundles';
    protected $fillable = [
        'name','description','seller_id','bundle_price',
        'original_total','bundle_image','is_active','status',
    ];
    protected $casts = [
        'bundle_price'   => 'decimal:2',
        'original_total' => 'decimal:2',
        'is_active'      => 'boolean',
    ];
    public function seller() { return $this->belongsTo(Seller::class); }
    public function items()  { return $this->hasMany(BundleItem::class, 'bundle_id'); }
    public function products(){
        return $this->belongsToMany(Product::class, 'bundle_items', 'bundle_id', 'product_id')
            ->withPivot('quantity');
    }
    public function getSavingsAttribute(): float {
        return max(0, ($this->original_total ?? 0) - $this->bundle_price);
    }
}