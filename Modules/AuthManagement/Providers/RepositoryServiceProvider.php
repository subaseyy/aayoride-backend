<?php

namespace Modules\AuthManagement\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //service
        $servicesPath = base_path('Modules/AuthManagement/Service');
        $serviceInterfacePath = base_path('Modules/AuthManagement/Service/Interface');
        $serviceFiles = File::files($servicesPath);
        foreach ($serviceFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $interfaceName = $filename . 'Interface';
            $interfacePath = $serviceInterfacePath . DIRECTORY_SEPARATOR . $interfaceName . '.php';
            if (File::exists($interfacePath)) {
                $serviceInterface = 'Modules\AuthManagement\Service\Interface\\' . $interfaceName;
                $service = 'Modules\AuthManagement\Service\\' . $filename;
                $this->app->bind($serviceInterface, $service);
            }
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
