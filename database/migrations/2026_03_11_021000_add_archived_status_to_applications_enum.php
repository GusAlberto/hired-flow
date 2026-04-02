<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("ALTER TABLE applications MODIFY status ENUM('applied','waiting','interview','rejected','offer','archived') NOT NULL DEFAULT 'applied'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement("UPDATE applications SET status = 'waiting' WHERE status = 'archived'");
        DB::statement("ALTER TABLE applications MODIFY status ENUM('applied','waiting','interview','rejected','offer') NOT NULL DEFAULT 'applied'");
    }
};
