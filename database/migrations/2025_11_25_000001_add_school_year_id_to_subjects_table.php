<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->foreignId('school_year_id')
                ->nullable()
                ->after('grade_level_id')
                ->constrained('school_years')
                ->nullOnDelete();
        });

        $activeSchoolYearId = DB::table('school_years')
            ->where('status', 'active')
            ->value('id');

        if ($activeSchoolYearId) {
            DB::table('subjects')->update(['school_year_id' => $activeSchoolYearId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_year_id');
        });
    }
};

