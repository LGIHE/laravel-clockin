<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
     * Bootstrap the model and its traits.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_level_id',
        'designation_id',
        'department_id',
        'project_id',
        'name',
        'email',
        'phone',
        'employee_code',
        'password',
        'status',
        'ip',
        'last_in_time',
        'auto_punch_out_time',
        'archived_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_in_time' => 'datetime',
        'auto_punch_out_time' => 'datetime',
        'archived_at' => 'datetime',
        'status' => 'integer',
        'password' => 'hashed',
    ];

    /**
     * Get the user level that owns the user.
     */
    public function userLevel()
    {
        return $this->belongsTo(UserLevel::class, 'user_level_id');
    }

    /**
     * Get the department that owns the user.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the designation that owns the user.
     */
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    /**
     * Get the supervisors of the user.
     */
    public function supervisors()
    {
        return $this->belongsToMany(User::class, 'user_supervisor', 'user_id', 'supervisor_id')
                    ->withTimestamps()
                    ->withPivot('id');
    }

    /**
     * Get the users supervised by this user (inverse of supervisors).
     */
    public function supervisedUsers()
    {
        return $this->belongsToMany(User::class, 'user_supervisor', 'supervisor_id', 'user_id')
                    ->withTimestamps()
                    ->withPivot('id');
    }
    
    /**
     * Get the primary/first supervisor of the user.
     * Useful for legacy code that expects a single supervisor.
     */
    public function supervisor()
    {
        return $this->supervisors()->first();
    }

    /**
     * Get the attendances for the user.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    /**
     * Get the leaves for the user.
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class, 'user_id');
    }

    /**
     * Get the project that the user is assigned to.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get all projects assigned to the user (many-to-many).
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')
                    ->withTimestamps();
    }

    /**
     * Get the user's role name.
     */
    public function getRoleAttribute()
    {
        return $this->userLevel ? strtoupper($this->userLevel->name) : null;
    }

    /**
     * Scope a query to only include active users (not archived).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('archived_at');
    }

    /**
     * Scope a query to only include archived users.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus($query, $status)
    {
        if ($status === 'active') {
            return $query->where('status', 1)->whereNull('archived_at');
        } elseif ($status === 'deactivated') {
            return $query->where('status', 0)->whereNull('archived_at');
        } elseif ($status === 'archived') {
            return $query->whereNotNull('archived_at');
        }
        return $query;
    }

    /**
     * Archive the user (also deactivates the user).
     */
    public function archive()
    {
        $this->update([
            'archived_at' => now(),
            'status' => 0
        ]);
    }

    /**
     * Unarchive the user (also reactivates the user).
     */
    public function unarchive()
    {
        $this->update([
            'archived_at' => null,
            'status' => 1
        ]);
    }

    /**
     * Check if user is archived.
     */
    public function isArchived()
    {
        return !is_null($this->archived_at);
    }
}
