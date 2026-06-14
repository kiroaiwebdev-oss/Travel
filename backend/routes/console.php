<?php

use App\Services\Cashback\CashbackService;
use Illuminate\Support\Facades\Schedule;

// Mature confirmed cashback whose hold period has elapsed (pending -> withdrawable).
Schedule::call(function (CashbackService $cashback) {
    $cashback->matureDue();
})->hourly()->name('cashback:mature')->withoutOverlapping();

// Keep the Meilisearch offers index warm / prune expired cached offers daily.
Schedule::command('tc:prune-offers')->dailyAt('03:00')->name('offers:prune');
