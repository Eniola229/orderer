<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class WithdrawalRequest extends Model {
    use HasUuid;
    protected $fillable = [
        'seller_id', 'amount', 'bank_name', 'account_name',
        'account_number', 'bank_code', 'country_code', 'currency',
        'payout_type', 'mobile_money_operator', 'mobile_number',
        'status', 'note', 'processed_at', 'admin_note', 'processed_by',
        'korapay_reference', 'korapay_status', 'payout_fee', 'note'
    ];
    protected $casts = ['amount' => 'decimal:2', 'processed_at' => 'datetime'];
    public function seller() { return $this->belongsTo(Seller::class); }
    public function processedBy() { return $this->belongsTo(Admin::class, 'processed_by'); }
} 
