<?php

namespace Modules\ChattingManagement\Providers;

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
        $repositoriesPath = base_path('Modules/ChattingManagement/Repository/Eloquent');
        $repositoryInterfacePath = base_path('Modules/ChattingManagement/Repository');
        $repositoryFiles = File::files($repositoriesPath);
        foreach ($repositoryFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $interfaceName = $filename . 'Interface';
            $interfacePath = $repositoryInterfacePath . DIRECTORY_SEPARATOR . $interfaceName . '.php';
            if (File::exists($interfacePath)) {
                $interface = 'Modules\ChattingManagement\Repository\\' . $interfaceName;
                $repository = 'Modules\ChattingManagement\Repository\Eloquent\\' . $filename;
                $this->app->bind($interface, $repository);
            }
        }

        //service
        $servicesPath = base_path('Modules/ChattingManagement/Service');
        $serviceInterfacePath = base_path('Modules/ChattingManagement/Service/Interface');
        $serviceFiles = File::files($servicesPath);
        foreach ($serviceFiles as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            $interfaceName = $filename . 'Interface';
            $interfacePath = $serviceInterfacePath . DIRECTORY_SEPARATOR . $interfaceName . '.php';
            if (File::exists($interfacePath)) {
                $serviceInterface = 'Modules\ChattingManagement\Service\Interface\\' . $interfaceName;
                $service = 'Modules\ChattingManagement\Service\\' . $filename;
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
