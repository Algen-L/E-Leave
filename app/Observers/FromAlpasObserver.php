<?php

namespace App\Observers;

use App\Models\FromAlpas;

class FromAlpasObserver
{
    /**
     * Handle the FromAlpas "created" event.
     */
    public function created(FromAlpas $fromAlpas): void
    {
        app(\App\Services\AlpasDeductionService::class)->processRecord($fromAlpas);
    }

    /**
     * Handle the FromAlpas "updated" event.
     */
    public function updated(FromAlpas $fromAlpas): void
    {
        // Only trigger if credits have changed and not fully processed
        if ($fromAlpas->isDirty('leave_credits') && $fromAlpas->leave_credits > $fromAlpas->processed_credits) {
            app(\App\Services\AlpasDeductionService::class)->processRecord($fromAlpas);
        }
    }
}
