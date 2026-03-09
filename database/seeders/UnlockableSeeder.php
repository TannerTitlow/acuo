<?php

namespace Database\Seeders;

use App\Models\Unlockable;
use Illuminate\Database\Seeder;

class UnlockableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unlockable::insert([
            // Themes
            ['key' => 'theme_midnight', 'name' => 'Midnight', 'description' => 'A deep dark theme with blue accents.', 'category' => 'theme', 'unlock_condition' => 'reach_level_3'],
            ['key' => 'theme_forest', 'name' => 'Forest', 'description' => 'A calming green-toned theme.', 'category' => 'theme', 'unlock_condition' => 'reach_level_5'],
            ['key' => 'theme_slate', 'name' => 'Slate', 'description' => 'A cool grey slate theme.', 'category' => 'theme', 'unlock_condition' => 'reach_level_7'],
            ['key' => 'theme_ember', 'name' => 'Ember', 'description' => 'A warm amber and red theme.', 'category' => 'theme', 'unlock_condition' => 'complete_10_projects'],
            ['key' => 'theme_void', 'name' => 'Void', 'description' => 'An ultra-dark premium theme for masters.', 'category' => 'theme', 'unlock_condition' => 'reach_level_15'],
            // Features
            ['key' => 'brain_dump_templates', 'name' => 'Brain Dump Templates', 'description' => 'Predefined templates to speed up brain dumps.', 'category' => 'feature', 'unlock_condition' => 'reach_level_4'],
            ['key' => 'advanced_stats', 'name' => 'Advanced Stats', 'description' => 'Detailed planning accuracy and trend charts.', 'category' => 'feature', 'unlock_condition' => 'reach_level_6'],
            ['key' => 'custom_block_colors', 'name' => 'Custom Block Colors', 'description' => 'Color-code your schedule blocks.', 'category' => 'feature', 'unlock_condition' => 'reach_level_8'],
            ['key' => 'priority_replan', 'name' => 'Priority Replan', 'description' => 'Jump the AI replan queue.', 'category' => 'feature', 'unlock_condition' => 'reach_level_10'],
            ['key' => 'ai_session_warmup', 'name' => 'AI Session Warmup', 'description' => 'AI pre-loads your context before each session.', 'category' => 'feature', 'unlock_condition' => 'reach_level_12'],
            // Cosmetics
            ['key' => 'custom_streak_icon', 'name' => 'Custom Streak Icon', 'description' => 'Choose your own streak display icon.', 'category' => 'cosmetic', 'unlock_condition' => 'maintain_7_day_streak'],
            ['key' => 'dashboard_layouts', 'name' => 'Dashboard Layouts', 'description' => 'Rearrange your dashboard widgets.', 'category' => 'cosmetic', 'unlock_condition' => 'reach_level_9'],
            ['key' => 'acuo_badge', 'name' => 'Acuo Badge', 'description' => 'The elite badge for reaching max level.', 'category' => 'cosmetic', 'unlock_condition' => 'reach_level_15'],
        ]);
    }
}
