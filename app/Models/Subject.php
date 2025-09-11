<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'description'
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
}
