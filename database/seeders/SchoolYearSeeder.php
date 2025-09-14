<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SchoolYear;

class SchoolYearSeeder extends Seeder
{
    public function run(): void
    {
        SchoolYear::firstOrCreate(
            ['name' => '2025-2026'], 
            [
                'start_date' => '2025-06-01',
                'end_date' => '2026-03-31',
                'status' => 'active'
            ]
        );
    }
}
