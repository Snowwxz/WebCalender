<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Agenda;

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
        View::composer('layouts.header', function ($view) {
            $notificationCount = 0;
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->role === 'superadmin') {
                    $notificationCount = Agenda::where('status', 'pending')->count();
                } elseif ($user->role === 'admin') {
                    $notificationCount = Agenda::where('status', 'pending')
                                                ->whereHas('unit', function ($query) use ($user) {
                                                    $query->where('id_unit', $user->unit_id);
                                                })
                                                ->count();
                } else { // user/OPD
                    $notificationCount = Agenda::where('status', 'approved')
                                                ->where('submitted_by', $user->id)
                                                ->count() + Agenda::where('status', 'rejected')
                                                ->where('submitted_by', $user->id)
                                                ->count();
                }
            }
            $view->with('notificationCount', $notificationCount);
        });
    }
}
