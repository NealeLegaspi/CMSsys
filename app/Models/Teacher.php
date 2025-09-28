<?php
// app/Models/Teacher.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id')
                    ->withPivot('section_id')
                    ->withTimestamps();
    }

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'subject_teacher', 'teacher_id', 'section_id')
                    ->withPivot('subject_id')
                    ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'teacher_id', 'user_id');
    }
}
