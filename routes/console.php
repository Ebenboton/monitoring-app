<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



Schedule::command('monitor:check-all')->everyMinute();
Schedule::command('monitor:ssl-check')->dailyAt('06:00');
Schedule::command('monitor:cleanup')->dailyAt('02:00');


Schedule::command('monitor:check-all')->everyMinute();
Schedule::command('monitor:escalate')->everyMinute();
Schedule::command('monitor:ssl-check')->dailyAt('06:00');
Schedule::command('monitor:cleanup')->dailyAt('02:00');