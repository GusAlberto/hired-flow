<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'salary_offered')) {
                $table->decimal('salary_offered', 12, 2)->nullable()->after('personal_score');
            }

            if (!Schema::hasColumn('applications', 'salary_expected')) {
                $table->decimal('salary_expected', 12, 2)->nullable()->after('salary_offered');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('applications', 'salary_offered')) {
                $dropColumns[] = 'salary_offered';
            }

            if (Schema::hasColumn('applications', 'salary_expected')) {
                $dropColumns[] = 'salary_expected';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
