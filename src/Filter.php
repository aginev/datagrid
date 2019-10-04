<?php

namespace Aginev\Datagrid;

/**
 * Description of Filter
 *
 * @author Atanas Ginev
 */
class Filter
{

    private $key = '';
    private $value = '';

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Get filter key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set filter key
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
     * Get filter value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set filter value
     *
     * @param $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
