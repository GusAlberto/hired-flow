<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->date('interview_date')->nullable()->after('job_url');
            $table->time('interview_time')->nullable()->after('interview_date');
            $table->string('interview_location')->nullable()->after('interview_time');
            $table->boolean('interview_is_remote')->default(false)->after('interview_location');
            $table->string('interview_platform')->nullable()->after('interview_is_remote');
            $table->string('interview_address')->nullable()->after('interview_platform');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'interview_date',
                'interview_time',
                'interview_location',
                'interview_is_remote',
                'interview_platform',
                'interview_address',
            ]);
        });
    }
};