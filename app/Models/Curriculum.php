<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curriculum extends Model
{
    protected $fillable = [
        'name',
        'school_year_id',
        'is_template',
    ];

    /**
     * Get the school year that owns the curriculum.
     */
    public function schoolYear(): BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }

    /**
     * Get the subjects for the curriculum.
     */
    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subjects')
                    ->withTimestamps();
    }

    /**
     * Get subjects grouped by grade level.
     */
    public function getSubjectsByGradeLevel()
    {
        return $this->subjects()
            ->with('gradeLevel')
            ->get()
            ->groupBy('grade_level_id')
            ->map(function ($subjects) {
                return $subjects->map(function ($subject) {
                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'grade_level' => $subject->gradeLevel->name ?? 'N/A',
                    ];
                });
            });
    }
}


