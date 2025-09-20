<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'gradelevel_id', 'school_year_id', 'adviser_id'];

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'gradelevel_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher')
                    ->withPivot('subject_id')
                    ->withTimestamps();
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
