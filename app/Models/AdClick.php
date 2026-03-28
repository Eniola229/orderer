<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class AdClick extends Model {
    use HasUuid;
    protected $table = 'ad_clicks';
    protected $fillable = ['ad_id','user_id','ip_address','user_agent'];
    public function ad() { return $this->belongsTo(Ad::class); }
}
