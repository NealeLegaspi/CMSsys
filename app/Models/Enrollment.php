<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'section_id',
        'school_year',
        'status'
    ];

    public function students()
    {
        return $this->hasManyThrough(User::class, Enrollment::class, 'section_id', 'id', 'id', 'student_id');
    }


    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
