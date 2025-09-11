<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            ['name' => 'Section A', 'gradelevel_id' => 1],
            ['name' => 'Section B', 'gradelevel_id' => 1],
            ['name' => 'Section C', 'gradelevel_id' => 2],
        ];

        foreach (range(1, 6) as $gradeLevelId) {
            foreach (['A', 'B', 'C'] as $letter) {
                Section::firstOrCreate([
                    'name' => "Section {$letter}",
                    'gradelevel_id' => $gradeLevelId,
                ]);
            }
        }
    }
}
