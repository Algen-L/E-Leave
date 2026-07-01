<?php

namespace App\Observers;

use App\Models\FromDtr;
use App\Services\DtrDeductionService;

class FromDtrObserver
{
    /**
     * Handle the FromDtr "created" event.
     */
    public function created(FromDtr $fromDtr): void
    {
        // When a new DTR record is added, check if the user has accumulated enough minutes for a deduction
        app(DtrDeductionService::class)->processUserDeduction($fromDtr->employee_number);
    }
}
