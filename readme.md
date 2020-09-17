# Datagrid For Laravel 5+
Package that easily converts collection of models to a datagrid table. The main goal of the package is to build for you a table with sorting and filters per column. You are defining the grid structure in your controller, pass the datagrid to the view and show it there. This will give you a really clean views, just a single line to show the table + filters + sorting + pagination. Keep in mind that filtering and sorting the data is up to you!

## Features
- Composer installable
- PSR4 autoloading
- Has filters row
- Has columns sort order
- Easily can add action column with edit/delete/whatever links
- Ability to modify cell data via closure function
- Bootstrap friendly
- Columns has data attributes based on a column data key

## Requires
Build to be used with Laravel only!

## Installation
Require package at your composer.json file like so
```json
{
    "require": {
        "aginev/datagrid": "2.0.*"
    }
}
```

Tell composer to update your dependencies
```sh
composer update
```

Or in terminal
```sh
composer require aginev/datagrid:1.0.*
```

## HOWTO
Let's consider that we have users and user roles (roles) table at our system.

### Users table

**id:** primary key

**role_id:** foreign key to roles table primary key

**email:** user email added used as username

**first_name:** user first name

**last_name:** user last name

**password:** hashed password

**created_at:** when it's created

**updated_at:** when is the latest update

### Roles Table

**id:** primary key

**title:** Role title e.g. Administrators Access

**created_at:** when it's created

**updated_at:** when is the latest update

We need a table with all the users data, their roles, edit and delete links at the last column at the table, filters and sort links at the very top, pagination at the very bottom.

```php
<?php

// Grap all the users with their roles
// NB!!! At the next line you are responsible for data filtration and sorting!
$users = User::with('role')->paginate(25);

// Create Datagrid instance
// You need to pass the users and the URL query params that the package is using
$grid = new \Datagrid($users, Request::get('f', []));

// Or if you do not want to use the alias
//$grid = new \Aginev\Datagrid\Datagrid($users, Request::get('f', []));

// Then we are starting to define columns
$grid
	->setColumn('first_name', 'First Name', [
		// Will be sortable column
		'sortable'    => true,
		// Will have filter
		'has_filters' => true
	])
	->setColumn('email', 'Email', [
		'sortable'    => true,
		'has_filters' => true,
		// Wrapper closure will accept two params
		// $value is the actual cell value
		// $row are the all values for this row
		'wrapper'     => function ($value, $row) {
			return '<a href="mailto:' . $value . '">' . $value . '</a>';
		}
	])
	->setColumn('role_id', 'Role', [
		// If you want to have role_id in the URL query string but you need to show role.name as value (dot notation for the user/role relation)
		'refers_to'   => 'role.name',
		'sortable'    => true,
		'has_filters' => true,
		// Pass array of data to the filter. It will generate select field.
		'filters'     => Role::all()->lists('title', 'id'),
		// Define HTML attributes for this column
		'attributes'  => [
            'class'         => 'custom-class-here',
            'data-custom'   => 'custom-data-attribute-value',
        ],
	])
	->setColumn('created_at', 'Created', [
		'sortable'    => true,
		'has_filters' => true,
		'wrapper'     => function ($value, $row) {
			// The value here is still Carbon instance, so you can format it using the Carbon methods
			return $value;
		}
	])
	->setColumn('updated_at', 'Updated', [
		'sortable'    => true,
		'has_filters' => true
	])
	// Setup action column
	->setActionColumn([
		'wrapper' => function ($value, $row) {
			return '<a href="' . action('HomeController@index', $row->id) . '" title="Edit" class="btn btn-xs"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
					<a href="' . action('HomeController@index', $row->id) . '" title="Delete" data-method="DELETE" class="btn btn-xs text-danger" data-confirm="Are you sure?"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';
		}
	]);

// Finally pass the grid object to the view
return view('grid', ['grid' => $grid]);
```

Lets show the grid in the view. grid-table param is not required and it's the id of the table.
```blade
...
{!! $grid->show('grid-table') !!}
...
```

### Modifying Default View

```sh
php artisan vendor:publish --provider="Aginev\Datagrid\DatagridServiceProvider" --tag="views"
```

This will copy the view to `resources/views/vendor/datagrid/datagrid.blade.php`. Editing this file you will be able to modify the grid view as you like with no chance to loose your changes.

### Modifying Config

```sh
php artisan vendor:publish --provider="Aginev\Datagrid\DatagridServiceProvider" --tag="config"
```

This will copy the config to `config/datagrid.php`.


## License
MIT - http://opensource.org/licenses/MIT
