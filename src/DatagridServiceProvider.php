<?php namespace Aginev\Datagrid;

use Illuminate\Support\ServiceProvider;

class DatagridServiceProvider extends ServiceProvider
{
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $namespace = 'datagrid';
        
        // Tell Laravel where the views for a given namespace are located.
        $this->loadViewsFrom(__DIR__ . '/../views', $namespace);
        
        // Publish package views
        $this->publishes([
            __DIR__ . '/../views/datagrid.blade.php' => resource_path('views/vendor/' . $namespace . '/datagrid.blade.php'),
        ], 'views');
        
        // Publish package config
        $this->publishes([
            __DIR__ . '/../config/datagrid.php' => config_path('datagrid.php'),
        ], 'config');
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datagrid.php', 'datagrid');
    }
}
