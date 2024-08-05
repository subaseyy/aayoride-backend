<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\TripManagement\Service\Interface\TripRequestServiceInterface;

class GlobalDataServiceProvider extends ServiceProvider
{


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {


        View::composer(
            'adminmodule::partials._sidebar',
            function ($view) {
                $tripCount = [];
                $view->with('tripCount', $this->getTripCounts());
            }
        );
    }



    private function getTripCounts()
    {
        $tripService = app()->make(TripRequestServiceInterface::class);

        return [
            'all' => $tripService->index()->count(),
            'completed' => $tripService->getBy(criteria:['current_status' => 'completed'])->count(),
            'pending' => $tripService->getBy(criteria:['current_status' => PENDING])->count(),
            'accepted' => $tripService->getBy(criteria:['current_status' => ACCEPTED])->count(),
            'ongoing' => $tripService->getBy(criteria:['current_status' => ONGOING])->count(),
            'cancelled' => $tripService->getBy(criteria:['current_status' => 'cancelled'])->count(),
        ];
    }
}
