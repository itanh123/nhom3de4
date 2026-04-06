<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dateTime('start_time')->nullable()->after('is_active');
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->enum('status', ['draft', 'scheduled', 'open', 'closed', 'archived'])
                ->default('draft')
                ->after('end_time');
            $table->boolean('is_published')->default(false)->after('status');
            $table->index(['start_time', 'end_time']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['start_time', 'end_time']);
            $table->dropIndex(['status']);
            $table->dropColumn(['start_time', 'end_time', 'status', 'is_published']);
        });
    }
};
