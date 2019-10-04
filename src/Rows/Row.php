<?php

namespace Aginev\Datagrid\Rows;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Aginev\Datagrid\Exceptions\CellException;

/**
 * Description of Row
 *
 * @author Atanas Ginev
 */
abstract class Row implements RowInterface
{

    /**
     * Row cells
     *
     * @var mixed
     */
    protected $data;

    public function __construct($row)
    {
        $this->setData($row);
    }

    /**
     * Get data for a specific key. If key not found null will be returned.
     *
     * @param $key
     *
     * @return mixed|null
     */
    public function __get($key)
    {
        // By default acts as array
        try {
            return $this->getData()[$key];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * get row data
     *
     * @return Collection
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get row instance based on the data type
     *
     * @param $row
     *
     * @return ArrayRow|CollectionRow|ModelRow|ObjectRow
     * @throws CellException
     */
    public static function getRowInstance($row)
    {
        if (is_array($row)) {
            return new \Aginev\Datagrid\Rows\ArrayRow($row);
        } else if ($row instanceof Model) {
            return new \Aginev\Datagrid\Rows\ModelRow($row);
        } else if ($row instanceof Collection) {
            return new \Aginev\Datagrid\Rows\CollectionRow($row);
        } else if (is_object($row)) {
            return new \Aginev\Datagrid\Rows\ObjectRow($row);
        }

        throw new CellException('Unsupported data!');
    }
}
