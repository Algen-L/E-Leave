<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\AutoAccrueCredits;
use App\Console\Commands\CheckMandatoryLeave;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automation: Run leave accrual daily at midnight
Schedule::command(AutoAccrueCredits::class)->daily();

// Automation: Run Mandatory Leave Compliance check on the last day of the year
Schedule::command(CheckMandatoryLeave::class)->yearlyOn(12, 31, '23:00');
