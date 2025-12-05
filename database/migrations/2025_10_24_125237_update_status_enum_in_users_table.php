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
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'sqlite') {
            // SQLite doesn't support MODIFY COLUMN or ENUM
            // The status column should already exist, we just ensure it has the right default
            // SQLite doesn't enforce ENUM constraints, so we skip the type change
            DB::statement("UPDATE users SET status = 'pending' WHERE status IS NULL OR status = ''");
        } else {
            // For MySQL/MariaDB
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active', 'inactive', 'rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse for SQLite
        $driver = DB::getDriverName();
        
        if ($driver !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        }
    }
};
