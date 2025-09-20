<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'section_id', 'student_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function profile()
    {
        return $this->hasOneThrough(
            UserProfile::class,
            User::class,
            'id',        // Foreign key on users table
            'user_id',   // Foreign key on user_profiles table
            'user_id',   // Local key on students table
            'id'         // Local key on users table
        );
    }
}
