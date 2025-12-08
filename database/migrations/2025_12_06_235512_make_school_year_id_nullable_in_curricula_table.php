<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['school_year_id']);
            
            // Make school_year_id nullable
            $table->unsignedBigInteger('school_year_id')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable support
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curricula', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['school_year_id']);
            
            // Make school_year_id not nullable again
            $table->unsignedBigInteger('school_year_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('school_year_id')
                  ->references('id')
                  ->on('school_years')
                  ->onDelete('cascade');
        });
    }
};
