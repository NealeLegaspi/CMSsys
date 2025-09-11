<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        // Subjects per grade level
        $subjectsByGrade = [
            1 => ['English', 'Mathematics', 'Science', 'Filipino', 'Araling Panlipunan'],
            2 => ['English', 'Mathematics', 'Science', 'Filipino', 'Araling Panlipunan', 'MAPEH'],
            3 => ['English', 'Mathematics', 'Science', 'Filipino', 'Araling Panlipunan', 'MAPEH', 'EPP/TLE'],
            4 => ['English', 'Mathematics', 'Science', 'Filipino', 'Araling Panlipunan', 'MAPEH', 'EPP/TLE', 'Values Education'],
            5 => ['English', 'Mathematics', 'Science', 'Filipino', 'Araling Panlipunan', 'MAPEH', 'EPP/TLE', 'Values Education'],
            6 => ['English', 'Mathematics', 'Science', 'Filipino', 'Araling Panlipunan', 'MAPEH', 'EPP/TLE', 'Values Education'],
        ];

        foreach ($subjectsByGrade as $gradeLevelId => $subjects) {
            foreach ($subjects as $sub) {
                Subject::firstOrCreate([
                    'name'           => $sub,
                    'grade_level_id' => $gradeLevelId,
                ]);
            }
        }
    }
}
