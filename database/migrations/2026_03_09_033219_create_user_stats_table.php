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
        Schema::create('user_stats', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('total_projects_completed')->default(0);
            $table->unsignedInteger('total_tasks_completed')->default(0);
            $table->unsignedInteger('total_habits_logged')->default(0);
            $table->unsignedInteger('current_task_streak')->default(0);
            $table->decimal('avg_estimate_accuracy', 5, 2)->nullable();
            $table->unsignedInteger('planning_score')->default(0);
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_stats');
    }
};
