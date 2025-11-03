<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('status', [
                'For Verification',
                'Enrolled',
                'Dropped',
                'Transferred'
            ])->default('For Verification')->change();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->enum('status', [
                'enrolled',
                'dropped',
                'transferred'
            ])->default('enrolled')->change();
        });
    }
};
