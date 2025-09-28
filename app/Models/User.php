<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'last_login_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
    ];


    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    protected static function booted()
    {
        static::created(function ($user) {
            if (!$user->profile) {
                $user->profile()->create([
                    'profile_picture' => 'images/default.png',
                ]);
            }
        });
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'posted_by');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'student_id');
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher')
                    ->withPivot('section_id')
                    ->withTimestamps();
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function getFullNameAttribute()
    {
        if ($this->profile) {
            return trim("{$this->profile->first_name} {$this->profile->last_name}");
        }

        return $this->email ?? 'Unknown User';
    }
}
