<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class PriceDropAlert extends Model {
    use HasUuid;
    protected $table = 'price_drop_alerts';
    protected $fillable = ['user_id','product_id','target_price','notified'];
    protected $casts = ['target_price' => 'decimal:2', 'notified' => 'boolean'];
    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
}