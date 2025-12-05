<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Check if column already exists (for SQLite compatibility)
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            // For SQLite, check if column exists before adding
            $columns = DB::select("PRAGMA table_info(subject_assignments)");
            $columnExists = collect($columns)->contains(function ($column) {
                return $column->name === 'approved_quarters';
            });
            
            if (!$columnExists) {
                Schema::table('subject_assignments', function (Blueprint $table) {
                    $table->json('approved_quarters')->nullable();
                });
            }
        } else {
            // For MySQL, check if column exists
            $columnExists = DB::select("SHOW COLUMNS FROM subject_assignments LIKE 'approved_quarters'");
            
            if (empty($columnExists)) {
                Schema::table('subject_assignments', function (Blueprint $table) {
                    $table->json('approved_quarters')->nullable()->after('grade_status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_assignments', function (Blueprint $table) {
            $table->dropColumn('approved_quarters');
        });
    }
};
