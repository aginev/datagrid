<?php namespace Aginev\Datagrid;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class DatagridServiceProvider extends ServiceProvider {

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		// Tell Laravel where the views for a given namespace are located.
		$this->loadViewsFrom(__DIR__ . '/Views', 'datagrid');

		$this->publishes([
			__DIR__ . '/Views' => base_path('resources/views/vendor/datagrid'),
		]);
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->register('Illuminate\Html\HtmlServiceProvider');

		AliasLoader::getInstance()->alias("Form", 'Illuminate\Html\FormFacade');
		AliasLoader::getInstance()->alias("Html", 'Illuminate\Html\HtmlFacade');
		AliasLoader::getInstance()->alias("Datagrid", 'Aginev\Datagrid\Datagrid');
	}
}