<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'grade_level_id',
    ];

    public function grades()
    {
        return $this->hasMany(Grade::class, 'subject_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'subject_teacher')
                    ->withPivot('section_id')
                    ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id');
    }
}
