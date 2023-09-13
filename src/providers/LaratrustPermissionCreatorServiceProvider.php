<?php

namespace Gopex\LaratrustPermissionCreator\providers;

use Gopex\LaratrustPermissionCreator\commands\FromConfig;
use Gopex\LaratrustPermissionCreator\commands\FromDB;
use Gopex\LaratrustPermissionCreator\Facades\PermissionConfigs;
use Illuminate\Support\ServiceProvider;
use Laratrust\Http\Controllers\PermissionsController;

class LaratrustPermissionCreatorServiceProvider extends ServiceProvider
{

     public function boot()
     {
        $this->commands([
            FromConfig::class,
            FromDB::class
        ]);

        $this->publishes([
            __DIR__ . '/../laratrust' => config_path() . '/../laratrust'
        ], ["laratrust-permissions-file"]);
     }

     public function register()
     {
         $this->app->singleton('laratrust_permissions_config', function(){return new PermissionConfigs();});
     }
}
