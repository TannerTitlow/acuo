<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Level::insert([
            ['id' => 1, 'title' => 'Dull', 'xp_required' => 0, 'unlock_key' => null],
            ['id' => 2, 'title' => 'Rough', 'xp_required' => 150, 'unlock_key' => null],
            ['id' => 3, 'title' => 'Grinding', 'xp_required' => 400, 'unlock_key' => 'theme_midnight'],
            ['id' => 4, 'title' => 'Honing', 'xp_required' => 800, 'unlock_key' => 'brain_dump_templates'],
            ['id' => 5, 'title' => 'Edged', 'xp_required' => 1400, 'unlock_key' => 'theme_forest'],
            ['id' => 6, 'title' => 'Keen', 'xp_required' => 2200, 'unlock_key' => 'advanced_stats'],
            ['id' => 7, 'title' => 'Whetted', 'xp_required' => 3200, 'unlock_key' => 'theme_slate'],
            ['id' => 8, 'title' => 'Fine', 'xp_required' => 4500, 'unlock_key' => 'custom_block_colors'],
            ['id' => 9, 'title' => 'Polished', 'xp_required' => 6000, 'unlock_key' => 'dashboard_layouts'],
            ['id' => 10, 'title' => 'Sharp', 'xp_required' => 8000, 'unlock_key' => 'priority_replan'],
            ['id' => 11, 'title' => 'Acute', 'xp_required' => 10500, 'unlock_key' => null],
            ['id' => 12, 'title' => 'Precise', 'xp_required' => 13500, 'unlock_key' => 'ai_session_warmup'],
            ['id' => 13, 'title' => 'Honed', 'xp_required' => 17000, 'unlock_key' => null],
            ['id' => 14, 'title' => 'Razor', 'xp_required' => 21000, 'unlock_key' => null],
            ['id' => 15, 'title' => 'Acuo', 'xp_required' => 26000, 'unlock_key' => 'theme_void'],
        ]);
    }
}
