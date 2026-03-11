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
            $table->decimal('salary_offered', 12, 2)->nullable()->after('personal_score');
            $table->decimal('salary_expected', 12, 2)->nullable()->after('salary_offered');
            $table->text('job_summary')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'salary_offered',
                'salary_expected',
                'job_summary',
            ]);
        });
    }
};
