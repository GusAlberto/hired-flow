<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE applications MODIFY status ENUM('applied','waiting','interview','rejected','offer','archived') NOT NULL DEFAULT 'applied'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE applications SET status = 'waiting' WHERE status = 'archived'");
        DB::statement("ALTER TABLE applications MODIFY status ENUM('applied','waiting','interview','rejected','offer') NOT NULL DEFAULT 'applied'");
    }
};
