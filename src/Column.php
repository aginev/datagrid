<?php

namespace Aginev\Datagrid;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Aginev\Datagrid\Exceptions\ColumnException;

/**
 * Description of Column
 *
 * @author Atanas Ginev
 */
class Column
{

    /**
     * Column title
     *
     * @var string
     */
    private $title = '';

    /**
     * Row key
     *
     * @var string
     */
    private $key = '';

    /**
     * Overwrites $key on data extraction
     *
     * @var string
     */
    private $refers_to = false;

    /**
     * Is sortable column
     *
     * @var bool
     */
    private $sortable = false;

    /**
     * Did the column requires filters
     *
     * @var bool
     */
    private $has_filters = false;

    /**
     * If $filters is set to array and $filters_multiple is set to true
     * it will build <select multiple="multiple"> field.
     *
     * @var bool
     */
    private $filter_many = false;

    /**
     * If filterable define filter values. If collection is passed
     * and the values is different from FALSE it will build <select>.
     * Otherwise it will build <input> field.
     *
     * @var mixed
     */
    private $filters = false;

    /**
     * Wrapping closure. Will be used when the cell needs extra data in it e.g.
     * email cells needs to be mail to links.
     *
     * @var \Closure
     */
    private $wrapper = null;

    /**
     * Is action column
     *
     * @var bool
     */
    private $is_action = false;

    /**
     * Column html attributes
     *
     * @var array
     */
    private $attributes = [];

    /**
     * Instance Column
     *
     * @param string $key
     * @param string $title
     * @param array  $config
     */
    public function __construct($key, $title, $config = [])
    {
        $this->setTitle($title);
        $this->setKey($key);
        $this->init($config);
    }

    /**
     * Call wrapping closure
     *
     * @param $method
     * @param $args
     *
     * @return mixed
     * @throws ColumnException
     */
    public function __call($method, $args)
    {
        if (is_callable([$this, $method])) {
            return call_user_func_array($this->$method, $args);
        }

        throw new ColumnException('Unable to call wrapping closure!');
    }

    /**
     * Get column key
     *
     * @param bool $refers_to
     *
     * @return string
     */
    public function getKey($refers_to = false)
    {
        if ($refers_to === true && $this->refers_to !== false) {
            return $this->refers_to;
        }

        return $this->key;
    }

    /**
     * Get refers to key
     *
     * @return string
     */
    public function getRefersTo()
    {
        return $this->refers_to;
    }

    /**
     * Set refers key
     *
     * @param bool $refers_to
     *
     * @return $this
     */
    public function setRefersTo($refers_to = false)
    {
        $this->refers_to = $refers_to;

        return $this;
    }

    /**
     * Set column key
     *
     * @param $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get column title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set column title
     *
     * @param $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Setup column by passing config array.
     *
     * @param array $config
     *
     * @return $this
     */
    public function init(array $config)
    {
        if ($config) {
            foreach ($config as $key => $value) {
                $method = 'set' . ucfirst(Str::camel($key));

                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
            }
        }

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Sortable
    |--------------------------------------------------------------------------
    */

    /**
     * If the column is sortable
     *
     * @return bool
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * Set as sortable column
     *
     * @param bool $sortable
     *
     * @return $this
     */
    public function setSortable($sortable = true)
    {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * Alias of getSortable
     *
     * @return bool
     */
    public function isSortable()
    {
        return $this->getSortable();
    }

    /**
     * Set sort key
     *
     * @param $sort_by
     *
     * @return $this
     */
    public function setSortBy($sort_by)
    {
        $this->sort_by = $sort_by;

        return $this;
    }

    /**
     * Set sort direction
     *
     * @param string $sort_dir
     *
     * @return $this
     */
    public function setSortDir($sort_dir = 'ASC')
    {
        $this->sort_dir = $sort_dir;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    /**
     * Get column filters
     *
     * @param bool $first_blank
     *
     * @return array
     */
    public function getFilters($first_blank = false)
    {
        $filters = $this->filters;

        if ($first_blank) {
            $filters = ['' => '---'] + $filters;
        }

        return $filters;
    }

    /**
     * Set column filters
     *
     * @param $filters
     *
     * @return $this
     */
    public function setFilters($filters)
    {
        if ($filters instanceof Collection) {
            $filters = $filters->toArray();
        }

        $this->filters = $filters;

        return $this;
    }

    /**
     * Did column has filters
     *
     * @return bool
     */
    public function hasFilters()
    {
        return $this->has_filters;
    }

    /**
     * Select multiple in filters row for that column
     *
     * @return bool
     */
    public function getFilterMany()
    {
        return $this->filter_many;
    }

    /**
     * Alias to getFilterMany
     *
     * @return bool
     */
    public function hasFilterMany()
    {
        return $this->getFilterMany();
    }

    /**
     * Set column to use many filters
     *
     * @param $filter_many
     *
     * @return $this
     */
    public function setFilterMany($filter_many)
    {
        $this->filter_many = $filter_many;

        return $this;
    }

    /**
     * Tell column to use filters
     *
     * @param bool $has_filters
     *
     * @return $this
     */
    public function setHasFilters($has_filters = true)
    {
        $this->has_filters = $has_filters;

        return $this;
    }

    /**
     * Get filter name
     *
     * @return string
     */
    public function getFilterName()
    {
        return 'f[' . $this->getKey() . ']' . ($this->hasFilterMany() ? '[]' : null);
    }

    /*
    |--------------------------------------------------------------------------
    | Action
    |--------------------------------------------------------------------------
    */

    /**
     * Is it action column
     *
     * @return bool
     */
    public function isAction()
    {
        return $this->is_action;
    }

    /**
     * Tell grid that this is action column
     *
     * @param bool $is_action
     *
     * @return $this
     */
    public function setAction($is_action = true)
    {
        $this->is_action = $is_action;

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Wrapper
    |--------------------------------------------------------------------------
    */

    /**
     * Get column wrapper
     *
     * @return callable
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * Set column wrapper
     *
     * @param callable $closure
     *
     * @return $this
     */
    public function setWrapper(\Closure $closure)
    {
        $this->wrapper = $closure;

        return $this;
    }

    /**
     * Did column has wrapper
     *
     * @return bool
     */
    public function hasWrapper()
    {
        if ($this->wrapper instanceof \Closure) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $key => $string_values) {
            $values = explode(' ', $string_values);

            // Trim the values
            $values = array_map('trim', $values);
            // Remove empty elements
            $values = array_filter($values);
            // Remove duplicate values
            $values = array_unique($values);

            $this->attributes[$key] = $values;
        }

        return $this;
    }

    public function getAttributesHtml()
    {
        $html = '';

        foreach ($this->getAttributes() as $attribute => $values) {
            $html .= $attribute . '="' . join(' ', $values) . '" ';
        }

        return trim($html);
    }
}
