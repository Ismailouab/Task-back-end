<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('due_date');
            $table->date('start_date')->nullable()->after('completed_at');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');
            $table->date('start_date')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'start_date']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['completed_at', 'start_date']);
        });
    }
};
