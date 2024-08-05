<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Modules\UserManagement\Entities\UserLevel;
use Modules\UserManagement\Policies\UserLevelPolicy;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        UserLevel::class => UserLevelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
       // Passport::routes();

        Gate::define('super-admin', fn () => auth()->user()->user_type == 'super-admin');

        Gate::define('dashboard', fn () => $this->checkAccess('dashboard', 'view'));

        Gate::define('zone_view', fn () => $this->checkAccess('zone_management', 'view'));
        Gate::define('zone_add', fn () => $this->checkAccess('zone_management', 'add'));
        Gate::define('zone_edit', fn () => $this->checkAccess('zone_management', 'update'));
        Gate::define('zone_delete', fn () => $this->checkAccess('zone_management', 'delete'));
        Gate::define('zone_log', fn () => $this->checkAccess('zone_management', 'log'));
        Gate::define('zone_export', fn () => $this->checkAccess('zone_management', 'export'));

        Gate::define('trip_view', fn () => $this->checkAccess('trip_management', 'view'));
        Gate::define('trip_edit', fn () => $this->checkAccess('trip_management', 'update'));
        Gate::define('trip_delete', fn () => $this->checkAccess('trip_management', 'delete'));
        Gate::define('trip_log', fn () => $this->checkAccess('trip_management', 'log'));
        Gate::define('trip_export', fn () => $this->checkAccess('trip_management', 'export'));

        Gate::define('parcel_view', fn () => $this->checkAccess('parcel_management', 'view'));
        Gate::define('parcel_add', fn () => $this->checkAccess('parcel_management', 'add'));
        Gate::define('parcel_edit', fn () => $this->checkAccess('parcel_management', 'update'));
        Gate::define('parcel_delete', fn () => $this->checkAccess('parcel_management', 'delete'));
        Gate::define('parcel_log', fn () => $this->checkAccess('parcel_management', 'log'));
        Gate::define('parcel_export', fn () => $this->checkAccess('parcel_management', 'export'));

        Gate::define('promotion_view', fn () => $this->checkAccess('promotion_management', 'view'));
        Gate::define('promotion_add', fn () => $this->checkAccess('promotion_management', 'add'));
        Gate::define('promotion_edit', fn () => $this->checkAccess('promotion_management', 'update'));
        Gate::define('promotion_delete', fn () => $this->checkAccess('promotion_management', 'delete'));
        Gate::define('promotion_log', fn () => $this->checkAccess('promotion_management', 'log'));
        Gate::define('promotion_export', fn () => $this->checkAccess('promotion_management', 'export'));

        Gate::define('vehicle_view', fn () => $this->checkAccess('vehicle_management', 'view'));
        Gate::define('vehicle_add', fn () => $this->checkAccess('vehicle_management', 'add'));
        Gate::define('vehicle_edit', fn () => $this->checkAccess('vehicle_management', 'update'));
        Gate::define('vehicle_delete', fn () => $this->checkAccess('vehicle_management', 'delete'));
        Gate::define('vehicle_log', fn () => $this->checkAccess('vehicle_management', 'log'));
        Gate::define('vehicle_export', fn () => $this->checkAccess('vehicle_management', 'export'));

        Gate::define('user_view', fn () => $this->checkAccess('user_management', 'view'));
        Gate::define('user_add', fn () => $this->checkAccess('user_management', 'add'));
        Gate::define('user_edit', fn () => $this->checkAccess('user_management', 'update'));
        Gate::define('user_delete', fn () => $this->checkAccess('user_management', 'delete'));
        Gate::define('user_log', fn () => $this->checkAccess('user_management', 'log'));
        Gate::define('user_export', fn () => $this->checkAccess('user_management', 'export'));

        Gate::define('transaction_view', fn () => $this->checkAccess('transaction_management', 'view'));
        Gate::define('transaction_export', fn () => $this->checkAccess('transaction_management', 'export'));

        Gate::define('business_view', fn () => $this->checkAccess('business_management', 'view'));
        Gate::define('business_edit', fn () => $this->checkAccess('business_management', 'update'));

    }

    private function checkAccess($module_name, $action){

        return auth()->user()->user_type == 'super-admin' ||
            (in_array($module_name, auth()->user()->role->modules) && auth()->user()->moduleAccess->where('module_name', $module_name)->first()?->$action);
    }
}
