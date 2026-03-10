<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('stage')->nullable()->after('location');
        });

        DB::table('applications')
            ->whereNull('stage')
            ->update([
                'stage' => DB::raw("CASE status
                    WHEN 'applied' THEN 'applied'
                    WHEN 'waiting' THEN 'waiting'
                    WHEN 'interview' THEN 'interview'
                    WHEN 'rejected' THEN 'rejected'
                    WHEN 'offer' THEN 'offer'
                    ELSE 'applied'
                END"),
            ]);
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
};