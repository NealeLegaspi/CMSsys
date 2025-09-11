<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'role_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id');
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

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
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
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
