<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// 'schedule_timezone' => env('APP_SCHEDULE_TIMEZONE', 'Asia/Shanghai'),
Schedule::command('app:download-pathway')->monthly()->runInBackground();