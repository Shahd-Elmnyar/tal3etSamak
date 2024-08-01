<?php

namespace App\Providers;

use App\Models\OrderDetail;
use App\Observers\OrderDetailObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        OrderDetail::observe(OrderDetailObserver::class);
    }
}
