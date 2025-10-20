<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompensationLeaveRequest extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'work_date',
        'work_type',
        'days_requested',
        'description',
        'status',
        'supervisor_approved_by',
        'supervisor_approved_at',
        'hr_effected_by',
        'hr_effected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'work_date' => 'date',
        'days_requested' => 'decimal:1',
        'supervisor_approved_at' => 'datetime',
        'hr_effected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supervisorApprover()
    {
        return $this->belongsTo(User::class, 'supervisor_approved_by');
    }

    public function hrEffector()
    {
        return $this->belongsTo(User::class, 'hr_effected_by');
    }
}
