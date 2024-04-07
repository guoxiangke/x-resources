<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test/cache', function () {
    $count = Cache::get('test_count', 0);
    Cache::set('test_count', ++$count);
    return [$count];
});


