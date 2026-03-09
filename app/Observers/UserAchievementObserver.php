<?php

namespace App\Observers;

use App\Models\UserAchievement;

class UserAchievementObserver
{
    /**
     * Handle the UserAchievement "created" event.
     */
    public function created(UserAchievement $userAchievement): void
    {
        //
    }

    /**
     * Handle the UserAchievement "updated" event.
     */
    public function updated(UserAchievement $userAchievement): void
    {
        //
    }

    /**
     * Handle the UserAchievement "deleted" event.
     */
    public function deleted(UserAchievement $userAchievement): void
    {
        //
    }

    /**
     * Handle the UserAchievement "restored" event.
     */
    public function restored(UserAchievement $userAchievement): void
    {
        //
    }

    /**
     * Handle the UserAchievement "force deleted" event.
     */
    public function forceDeleted(UserAchievement $userAchievement): void
    {
        //
    }
}
