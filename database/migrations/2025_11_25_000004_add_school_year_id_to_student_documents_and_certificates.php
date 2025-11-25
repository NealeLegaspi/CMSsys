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
        Schema::table('student_documents', function (Blueprint $table) {
            $table->foreignId('school_year_id')
                ->nullable()
                ->after('student_id')
                ->constrained('school_years')
                ->nullOnDelete();
        });

        Schema::table('student_certificates', function (Blueprint $table) {
            $table->foreignId('school_year_id')
                ->nullable()
                ->after('student_id')
                ->constrained('school_years')
                ->nullOnDelete();
        });

        $activeSY = DB::table('school_years')->where('status', 'active')->value('id');

        if ($activeSY) {
            DB::table('student_documents')->update(['school_year_id' => $activeSY]);
            DB::table('student_certificates')->update(['school_year_id' => $activeSY]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_year_id');
        });

        Schema::table('student_certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_year_id');
        });
    }
};

