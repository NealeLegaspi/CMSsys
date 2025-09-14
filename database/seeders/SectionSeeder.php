<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\SchoolYear;
use App\Models\GradeLevel;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $schoolYear = SchoolYear::where('status', 'active')->latest()->first();

        if (!$schoolYear) {
            $schoolYear = SchoolYear::create([
                'name' => '2025-2026',
                'start_date' => '2025-06-01',
                'end_date' => '2026-03-31',
                'status' => 'active',
            ]);
            $this->command->info("No active school year found. Created: {$schoolYear->name}");
        }

        $gradeLevels = GradeLevel::all();
        if ($gradeLevels->isEmpty()) {
            $this->command->error('No grade levels found. Please run GradeLevelSeeder first!');
            return;
        }

        foreach ($gradeLevels as $grade) {
            foreach (['A', 'B', 'C'] as $letter) {
                $section = Section::firstOrCreate([
                    'name' => "Section {$letter} - {$grade->name}",
                    'gradelevel_id' => $grade->id,
                    'school_year_id' => $schoolYear->id,
                ]);
                $this->command->info("Section seeded: {$section->name}");
            }
        }

        $this->command->info("All sections seeded successfully!");
    }
}
