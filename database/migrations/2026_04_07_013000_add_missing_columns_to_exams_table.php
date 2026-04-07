<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->timestamp('start_time')->nullable()->after('is_active');
            $table->timestamp('end_time')->nullable()->after('start_time');
            $table->string('status', 20)->default('draft')->after('end_time')
                  ->comment('draft, scheduled, open, closed, archived');
            $table->boolean('is_published')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['start_time', 'end_time', 'status', 'is_published']);
        });
    }
};
