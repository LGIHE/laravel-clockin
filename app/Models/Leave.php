<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'leave_category_id',
        'leave_status_id',
        'date',
        'end_date',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the user that owns the leave.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the category that owns the leave.
     */
    public function category()
    {
        return $this->belongsTo(LeaveCategory::class, 'leave_category_id');
    }

    /**
     * Get the status that owns the leave.
     */
    public function status()
    {
        return $this->belongsTo(LeaveStatus::class, 'leave_status_id');
    }
}
