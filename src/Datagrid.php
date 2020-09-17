<?php namespace Aginev\Datagrid;

use Config;
use Aginev\Datagrid\Rows\Row;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Aginev\Datagrid\Exceptions\DataException;
use Illuminate\Contracts\Pagination\Paginator;
use Aginev\Datagrid\Exceptions\ColumnException;

/**
 * Description of Datagrid
 *
 * @author Atanas Ginev
 */
class Datagrid
{

    /*
     * Datagrid config
     *
     *  @var array
     */
    private $config = [];

    /**
     * Grid rows
     *
     * @var Collection
     */
    private $rows = null;

    /**
     * Grid columns
     *
     * @var Collection
     */
    private $columns = null;

    /**
     * Grid filters
     *
     * @var Collection
     */
    private $filters = null;

    /**
     * Hidden search form fields
     *
     * @var array
     */
    private $hiddens = [];

    /**
     * Did the Datagrid will have bulk <input type="checkbox"> fields.
     *
     * @var bool
     */
    private $has_bulks = false;

    /**
     * Pagination object
     *
     * @var \Illuminate\View\View
     */
    private $pagination = false;

    /**
     * Create new datagrid instance
     *
     * @param mixed $rows Data to be rendered. It can be array or \Illuminate\Support\Collection.
     * @param null  $filters
     * @param array $config
     *
     * @throws Exceptions\CellException
     */
    public function __construct($rows = null, $filters = null, array $config = [])
    {
        $this->rows = new Collection();
        $this->columns = new Collection();
        $this->filters = new Collection();

        $this->initPagination($rows);
        $this->setRows($rows);
        $this->setFilters($filters);

        $this->config = $config;
    }

    /**
     * Get datagrid HTML
     *
     * @param string|null $id
     *
     * @return mixed
     */
    public function show($id = null)
    {
        return View::make('datagrid::datagrid', ['grid' => $this, 'id' => $id])->render();
    }

    /*
    |--------------------------------------------------------------------------
    | Rows
    |--------------------------------------------------------------------------
    */

    /**
     * Set grid row
     *
     * @param $row
     *
     * @return $this
     * @throws Exceptions\CellException
     */
    public function setRow($row)
    {
        $this->rows->push(Row::getRowInstance($row));

        return $this;
    }

    /**
     * Get grid rows
     *
     * @return Collection
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * Set grid rows
     *
     * @param $rows
     *
     * @return $this
     * @throws Exceptions\CellException
     */
    public function setRows($rows)
    {
        if ($rows && count($rows) > 0) {
            foreach ($rows as $row) {
                $this->setRow($row);
            }
        }

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Columns
    |--------------------------------------------------------------------------
    */

    /**
     * Get column by key
     *
     * @param $column
     *
     * @return mixed
     * @throws ColumnException
     */
    public function getColumn($column)
    {
        if ($this->columns->has($column)) {
            return $this->columns[$column];
        }

        throw new ColumnException('Column not found!');
    }

    /**
     * Set column
     *
     * @param string $key
     * @param string $title
     * @param array  $config
     *
     * @return $this
     */
    public function setColumn($key, $title, $config = [])
    {
        $this->columns->put($key, new Column($key, $title, $config));

        return $this;
    }

    /**
     * Set grid action column
     * TODO Refactor this to be able to use setColumn method
     *
     * @param array  $config
     * @param string $key
     * @param string $title
     *
     * @return $this
     */
    public function setActionColumn($config = [], $key = 'actions', $title = 'Actions')
    {
        $column = new Column($key, $title, $config);
        $column->setAction(true);

        $this->columns->put($key, $column);

        return $this;
    }

    /**
     * Get grid columns
     *
     * @return Collection
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Get grid columns count
     *
     * @return int
     */
    public function getColumnsCount()
    {
        $count = count($this->getColumns());

        if ($this->isBulkable()) {
            $count++;
        }

        //TODO Check if there are actions and if so add 1 for the actions column
        return $count + 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    /**
     * Get single filter by key
     *
     * @param        $key
     * @param string $default_value Default value if key is not found
     *
     * @return mixed|string
     */
    public function getFilter($key, $default_value = '')
    {
        if ($this->filters->has($key)) {
            return $this->filters[$key];
        }

        return $default_value;
    }

    /**
     * Set single filter
     *
     * @param mixed $key   Filter key
     * @param mixed $value Filter value
     *
     * @return $this
     */
    public function setFilter($key, $value)
    {
        $this->filters->put($key, $value);

        return $this;
    }

    /**
     * Get all filters
     *
     * @param bool $to_array If true will return filters as array. Default set to false.
     *
     * @return array|Collection
     */
    public function getFilters($to_array = true)
    {
        if ($to_array === true) {
            return $this->filters->toArray();
        }

        return $this->filters;
    }

    /**
     * Set many filters
     *
     * @param array $filters Array of key => value pairs
     *
     * @return $this
     */
    public function setFilters($filters = [])
    {
        if ($filters) {
            foreach ($filters as $key => $value) {
                $this->setFilter($key, $value);
            }
        }

        return $this;
    }

    /**
     * Check if the grid has a specific filter
     *
     * @param $key
     *
     * @return mixed|string
     */
    public function hasFilter($key)
    {
        return $this->getFilter($key);
    }

    /**
     * Check if the datagrid has filters. Will loop through all columns
     * and if any of the columns has filters this will mean that the datagrid
     * will have filters in general.
     *
     * @return boolean
     */
    public function hasFilters()
    {
        foreach ($this->columns as $col) {
            if ($col->hasFilters() === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get array of data for sort links at the header of the grid
     *
     * @param $field
     *
     * @return array
     */
    public function getSortParams($field)
    {
        // Clone the filters object. If you do not do this it will be passed
        // by reference and the values will be modified!
        $filters = clone $this->getFilters(false);

        if (!$filters->has('order_by')) {
            $filters->put('order_by', '');
        }

        if (!$filters->has('order_dir')) {
            $filters->put('order_dir', 'ASC');
        }

        if ($filters['order_by'] == $field) {
            if (!$filters->has('order_dir') || $filters['order_dir'] == 'ASC') {
                $filters->put('order_dir', 'DESC');
            } else {
                $filters->put('order_dir', 'ASC');
            }
        } else {
            $filters->put('order_by', $field);
            $filters->put('order_dir', 'ASC');
        }

        $per_page = intval(Request::get('per_page', Config::get('pagination.per_page')));
        $per_page = $per_page > 0 ? $per_page : Config::get('pagination.per_page');

        return ['f' => $filters->toArray(), 'page' => 1, 'per_page' => $per_page];
    }

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    /**
     * Init the grid pagination
     *
     * @param $rows
     */
    public function initPagination($rows)
    {
        if ($rows instanceof Paginator) {
            $this->setPagination($rows->appends(Request::all()));
        }
    }

    /**
     * Get grid pagination
     *
     * @return \Illuminate\View\View
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * Set grid pagination
     *
     * @param Paginator $pagination
     *
     * @return $this
     */
    public function setPagination(Paginator $pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * Check if the grid set has pagination
     *
     * @return bool
     */
    public function hasPagination()
    {
        return (bool)$this->getPagination();
    }

    /*
    |--------------------------------------------------------------------------
    | Config
    |--------------------------------------------------------------------------
    */

    /**
     * Get config item by key
     *
     * @param $key
     *
     * @return mixed
     * @throws DataException
     */
    public function getConfig($key)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        throw new DataException('Configuration value not found!');
    }

    /**
     * Set custom configuration values
     *
     * @param array $config Array of key => value pairs
     *
     * @return $this
     */
    public function setConfig(array $config = [])
    {
        $this->config = array_replace($this->config, $config);

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Bulks
    |--------------------------------------------------------------------------
    */

    /**
     * Tell grid that it needs to have bulk checkboxes
     *
     * @param bool $flag   Default false but when set to column from the result set it's values will be set as checkbox
     *                     value
     *
     * @return $this
     */
    public function isBulkable($flag = false)
    {
        $this->has_bulks = $flag;

        return $this;
    }

    /**
     * Did the grid has to have bulk checkboxes
     *
     * @return bool
     */
    public function isItBulkable()
    {
        return (bool)$this->has_bulks;
    }

    /**
     * Get bulk column
     */
    public function getBulk()
    {
        return $this->has_bulks;
    }

    /*
    |--------------------------------------------------------------------------
    | Hidden Fields
    |--------------------------------------------------------------------------
    */

    /**
     * Get grid hidden fields
     *
     * @return array
     */
    public function getHiddens()
    {
        return $this->hiddens;
    }

    /**
     * Set grid hidden fields
     *
     * @param array $hiddens
     *
     * @return $this
     */
    public function setHiddens($hiddens)
    {
        $this->hiddens = $hiddens;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     *
     * @param array  $arr1
     * @param array  $arr2
     * @param string $separator
     *
     * @return array
     */
    public static function concatStringArrays($arr1, $arr2, $separator = '')
    {
        $result = [];

        foreach ($arr1 as $key => $value) {
            // Fill with values from first array
            $result[$key] = $value;

            // If the same key exists in second array concatenate the values
            if (array_key_exists($key, $arr2)) {
                $result[$key] .= $separator . $arr2[$key];
            }
        }

        // Loop in second array to check if there are any missing key value pairs in the result
        foreach ($arr2 as $key => $value) {
            if (!array_key_exists($key, $result)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Current route link
     *
     * @param array $get_params
     *
     * @return string
     */
    public static function getCurrentRouteLink($get_params = [])
    {
        $current_action = Route::current()->getAction();
        $controller = '\\' . $current_action['controller'];
        $parameters = Route::current()->parameters();

        return action($controller, $parameters) . ($get_params ? '?' . http_build_query($get_params) : '');
    }
}
