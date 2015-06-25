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
			base_path('vendor/aginev/datagrid/src/Views') => base_path('resources/views/vendor/datagrid'),
		], 'views');
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		// Register the HtmlServiceProvider
		App::register('Illuminate\Html\HtmlServiceProvider');

		// Add aliases to Form/Html Facade
		$loader = AliasLoader::getInstance();
		$loader->alias('Form', 'Illuminate\Html\FormFacade');
		$loader->alias('HTML', 'Illuminate\Html\HtmlFacade');

		// Add alias for datagrid
		$loader->alias('Datagrid', 'Aginev\Datagrid\Datagrid');
	}
}