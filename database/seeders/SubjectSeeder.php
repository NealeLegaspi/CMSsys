<?php
namespace Database\Seeders;

use App\Models\GradeLevel;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjectsByGrade = [
            'Kindergarten' => ['Health, well-Being, and Motor Development', 'Socio-Emotional Development', 'Mathematics', 'Language, Literacy, and Communication', 'Understanding the Physical and Natural Environment'],
            'Grade 1'      => ['Mother Tongue', 'Filipino', 'English', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'Edukasyon sa Pagpapakatao (EsP)'],
            'Grade 2'      => ['Mother Tongue', 'Filipino', 'English', 'Mathematics', 'Araling Panlipunan', 'MAPEH', 'Edukasyon sa Pagpapakatao (EsP)'],
            'Grade 3'      => ['Mother Tongue', 'Filipino', 'English', 'Mathematics', 'Science', 'Araling Panlipunan', 'MAPEH', 'Edukasyon sa Pagpapakatao (EsP)'],
            'Grade 4'      => ['Filipino', 'English', 'Mathematics', 'Science', 'Araling Panlipunan', 'Edukasyon sa Pagpapakatao (EsP)', 'Edukasyong Pantahanan at Pangkabuhayan (EPP)', 'MAPEH'],
            'Grade 5'      => ['Filipino', 'English', 'Mathematics', 'Science', 'Araling Panlipunan', 'Edukasyon sa Pagpapakatao (EsP)', 'Edukasyong Pantahanan at Pangkabuhayan (EPP)', 'MAPEH'],
            'Grade 6'      => ['Filipino', 'English', 'Mathematics', 'Science', 'Araling Panlipunan', 'Edukasyon sa Pagpapakatao (EsP)', 'Edukasyong Pantahanan at Pangkabuhayan (EPP)', 'MAPEH'],
        ];

        foreach ($subjectsByGrade as $gradeName => $subjects) {
            $gradeLevel = GradeLevel::where('name', $gradeName)->first();
            if ($gradeLevel) {
                foreach ($subjects as $sub) {
                    Subject::firstOrCreate([
                        'name'           => $sub,
                        'grade_level_id' => $gradeLevel->id,
                    ]);
                }
            }
        }
    }
}
