<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            GradeLevelSeeder::class,
            SectionSeeder::class,
            SubjectSeeder::class,
            SettingsTableSeeder::class,
            CurriculumSeeder::class,
        ]);
    }
}
