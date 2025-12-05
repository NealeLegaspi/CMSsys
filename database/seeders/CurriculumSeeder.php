<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curriculum;
use App\Models\SchoolYear;
use App\Models\Subject;

class CurriculumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all school years
        $schoolYears = SchoolYear::all();

        if ($schoolYears->isEmpty()) {
            $this->command->warn('No school years found. Please create school years first.');
            return;
        }

        foreach ($schoolYears as $schoolYear) {
            // Get all subjects for this school year
            $subjects = Subject::where('school_year_id', $schoolYear->id)
                ->where('is_archived', false)
                ->get();

            if ($subjects->isEmpty()) {
                $this->command->info("No subjects found for school year: {$schoolYear->name}. Skipping...");
                continue;
            }

            // Create a default curriculum for this school year
            $curriculum = Curriculum::firstOrCreate(
                [
                    'name' => "Default Curriculum - {$schoolYear->name}",
                    'school_year_id' => $schoolYear->id,
                ]
            );

            // Attach all subjects to this curriculum
            $curriculum->subjects()->syncWithoutDetaching($subjects->pluck('id')->toArray());

            $this->command->info("Created curriculum '{$curriculum->name}' with {$subjects->count()} subjects for school year {$schoolYear->name}.");
        }

        $this->command->info('Curriculum seeding completed!');
    }
}

