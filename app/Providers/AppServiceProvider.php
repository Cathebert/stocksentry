<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\StockTake;
use  App\Observers\StockTakeObserver;
use App\Models\Discrepancy;
use App\Models\Laboratory;
use Illuminate\Support\Facades\Auth;
use  App\Observers\DiscrepancyObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
         Paginator::useBootstrapFour();
     StockTake::observe(StockTakeObserver::class);
 Discrepancy::observe(DiscrepancyObserver::class);
 
if (Auth::check()) {
 view()->composer('*',function($view) {
$data=Laboratory::where('id',auth()->user()->laboratory_id)->select('lab_name')->first();

            $view->with('lab_name', $data->lab_name);
            $view->with('occupation',auth()->user()->occupation); 
        });
}
    }
}
