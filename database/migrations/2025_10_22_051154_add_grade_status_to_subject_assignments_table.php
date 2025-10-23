<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('subject_assignments', function (Blueprint $table) {
            $table->enum('grade_status', ['draft', 'submitted', 'approved', 'returned'])
                  ->default('draft')
                  ->after('teacher_id');
        });
    }

    public function down() {
        Schema::table('subject_assignments', function (Blueprint $table) {
            $table->dropColumn('grade_status');
        });
    }
};

