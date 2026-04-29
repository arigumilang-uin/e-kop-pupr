<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Commands
|--------------------------------------------------------------------------
| Sinkronkan status periode pinjaman setiap hari pukul 00:01 WIB.
| Ini memastikan database selalu sinkron dengan logika tanggal.
|
| Untuk mengaktifkan, pastikan crontab berisi:
|   * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
|
*/
Schedule::command('periode:sinkron-status')->dailyAt('00:01');
