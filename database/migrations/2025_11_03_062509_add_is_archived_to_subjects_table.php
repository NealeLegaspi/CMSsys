<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->boolean('is_archived')->default(false)->after('grade_level_id');
        });
    }

    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('is_archived');
        });
    }
};
