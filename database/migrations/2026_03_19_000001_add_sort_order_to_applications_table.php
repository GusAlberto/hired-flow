<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('status');
            $table->index(['user_id', 'status', 'sort_order']);
        });

        $applications = DB::table('applications')
            ->select(['id', 'user_id', 'status'])
            ->orderBy('user_id')
            ->orderBy('status')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        $counters = [];

        foreach ($applications as $application) {
            $key = $application->user_id . '|' . $application->status;
            $counters[$key] = ($counters[$key] ?? 0) + 1;

            DB::table('applications')
                ->where('id', $application->id)
                ->update(['sort_order' => $counters[$key]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status', 'sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
