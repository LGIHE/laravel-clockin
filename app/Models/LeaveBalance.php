<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'leave_category_id',
        'year',
        'total_days',
        'used_days',
        'carried_forward',
        'carryforward_expires_at',
    ];

    protected $casts = [
        'year' => 'integer',
        'total_days' => 'decimal:1',
        'used_days' => 'decimal:1',
        'carried_forward' => 'decimal:1',
        'carryforward_expires_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(LeaveCategory::class, 'leave_category_id');
    }

    /**
     * Get remaining days including carryforward if not expired
     */
    public function getRemainingDaysAttribute()
    {
        $remaining = $this->total_days - $this->used_days;
        
        // Add carryforward if not expired
        if ($this->carried_forward > 0 && $this->carryforward_expires_at) {
            if (now()->lte($this->carryforward_expires_at)) {
                $remaining += $this->carried_forward;
            }
        }
        
        return max(0, $remaining);
    }
}
