<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        Achievement::insert([
            // Progress (6)
            ['id' => (string) Str::uuid(), 'key' => 'first_task', 'title' => 'First Step', 'description' => 'Complete your first task.', 'icon' => '✅', 'category' => 'progress', 'xp_reward' => 25, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'tasks_10', 'title' => 'Getting Momentum', 'description' => 'Complete 10 tasks.', 'icon' => '🔟', 'category' => 'progress', 'xp_reward' => 50, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'tasks_50', 'title' => 'On a Roll', 'description' => 'Complete 50 tasks.', 'icon' => '🎯', 'category' => 'progress', 'xp_reward' => 100, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'tasks_100', 'title' => 'Century', 'description' => 'Complete 100 tasks.', 'icon' => '💯', 'category' => 'progress', 'xp_reward' => 200, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'first_project', 'title' => 'Project Starter', 'description' => 'Complete your first project.', 'icon' => '🏁', 'category' => 'progress', 'xp_reward' => 75, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'projects_5', 'title' => 'Project Veteran', 'description' => 'Complete 5 projects.', 'icon' => '🏆', 'category' => 'progress', 'xp_reward' => 150, 'created_at' => $now],
            // Streak (5)
            ['id' => (string) Str::uuid(), 'key' => 'streak_3', 'title' => 'Hat Trick', 'description' => 'Maintain a 3-day task streak.', 'icon' => '🔥', 'category' => 'streak', 'xp_reward' => 30, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'streak_7', 'title' => 'Week Warrior', 'description' => 'Maintain a 7-day task streak.', 'icon' => '⚡', 'category' => 'streak', 'xp_reward' => 75, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'streak_30', 'title' => 'Monthly Grind', 'description' => 'Maintain a 30-day task streak.', 'icon' => '🌙', 'category' => 'streak', 'xp_reward' => 200, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'habit_streak_7', 'title' => 'Habit Former', 'description' => 'Log a habit 7 days in a row.', 'icon' => '📅', 'category' => 'streak', 'xp_reward' => 60, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'habit_streak_30', 'title' => 'Habit Master', 'description' => 'Log a habit 30 days in a row.', 'icon' => '🧘', 'category' => 'streak', 'xp_reward' => 175, 'created_at' => $now],
            // Planning (6)
            ['id' => (string) Str::uuid(), 'key' => 'first_ai_project', 'title' => 'AI Assisted', 'description' => 'Generate your first AI project.', 'icon' => '🤖', 'category' => 'planning', 'xp_reward' => 40, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'first_replan', 'title' => 'Adaptive Planner', 'description' => 'Replan a project for the first time.', 'icon' => '🔄', 'category' => 'planning', 'xp_reward' => 35, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'accurate_estimate', 'title' => 'Sharp Eye', 'description' => 'Estimate a task within 10% of actual time.', 'icon' => '🎯', 'category' => 'planning', 'xp_reward' => 50, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'accuracy_80', 'title' => 'Precision Planner', 'description' => 'Achieve 80% average estimate accuracy.', 'icon' => '📐', 'category' => 'planning', 'xp_reward' => 100, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'first_brain_dump', 'title' => 'Mind Cleared', 'description' => 'Process your first brain dump.', 'icon' => '🧠', 'category' => 'planning', 'xp_reward' => 25, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'brain_dumps_10', 'title' => 'Clarity Seeker', 'description' => 'Process 10 brain dumps.', 'icon' => '💡', 'category' => 'planning', 'xp_reward' => 80, 'created_at' => $now],
            // Milestone (3)
            ['id' => (string) Str::uuid(), 'key' => 'level_5', 'title' => 'Edged', 'description' => 'Reach level 5.', 'icon' => '⚔️', 'category' => 'milestone', 'xp_reward' => 100, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'level_10', 'title' => 'Sharp', 'description' => 'Reach level 10.', 'icon' => '🗡️', 'category' => 'milestone', 'xp_reward' => 250, 'created_at' => $now],
            ['id' => (string) Str::uuid(), 'key' => 'level_15', 'title' => 'Acuo', 'description' => 'Reach the maximum level.', 'icon' => '👑', 'category' => 'milestone', 'xp_reward' => 500, 'created_at' => $now],
        ]);
    }
}
