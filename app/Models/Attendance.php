<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

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
        'in_time',
        'in_message',
        'out_time',
        'out_message',
        'worked',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'in_time' => 'datetime',
        'out_time' => 'datetime',
        'worked' => 'integer',
    ];

    /**
     * Get the user that owns the attendance.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the worked hours in human-readable format (HH:MM).
     */
    public function getWorkedHoursAttribute()
    {
        if (!$this->worked) {
            return '00:00';
        }

        $hours = floor($this->worked / 3600);
        $minutes = floor(($this->worked % 3600) / 60);
        
        return sprintf('%02d:%02d', $hours, $minutes);
    }
}
