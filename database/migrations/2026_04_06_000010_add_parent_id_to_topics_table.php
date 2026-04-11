<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('topics') || Schema::hasColumn('topics', 'parent_id')) {
            return;
        }

        Schema::table('topics', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('created_by')
                ->constrained('topics')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('topics') || ! Schema::hasColumn('topics', 'parent_id')) {
            return;
        }

        Schema::table('topics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};
