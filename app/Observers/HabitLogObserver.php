<?php

namespace App\Observers;

use App\Models\HabitLog;

class HabitLogObserver
{
    /**
     * Handle the HabitLog "created" event.
     */
    public function created(HabitLog $habitLog): void
    {
        //
    }

    /**
     * Handle the HabitLog "updated" event.
     */
    public function updated(HabitLog $habitLog): void
    {
        //
    }

    /**
     * Handle the HabitLog "deleted" event.
     */
    public function deleted(HabitLog $habitLog): void
    {
        //
    }

    /**
     * Handle the HabitLog "restored" event.
     */
    public function restored(HabitLog $habitLog): void
    {
        //
    }

    /**
     * Handle the HabitLog "force deleted" event.
     */
    public function forceDeleted(HabitLog $habitLog): void
    {
        //
    }
}
