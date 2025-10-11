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
        Schema::table('student_certificates', function (Blueprint $table) {
            $table->string('remarks')->nullable()->after('purpose');
            $table->unsignedBigInteger('issued_by')->nullable()->after('file_path');

            $table->foreign('issued_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('student_certificates', function (Blueprint $table) {
            $table->dropForeign(['issued_by']);
            $table->dropColumn(['remarks', 'issued_by']);
        });
    }
};
