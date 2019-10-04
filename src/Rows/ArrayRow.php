<?php namespace Aginev\Datagrid\Rows;

use Illuminate\Support\Arr;

class ArrayRow extends Row
{

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = Arr::dot($data);

        return $this;
    }
}
