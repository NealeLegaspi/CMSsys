<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'gradelevel_id', 'adviser_id', 'school_year_id'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'gradelevel_id');
    }

    public function adviser()
    {
        return $this->belongsTo(User::class, 'adviser_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher')
                    ->withPivot('subject_id')
                    ->withTimestamps();
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }
}
