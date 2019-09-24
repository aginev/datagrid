<?php

namespace Aginev\Datagrid\Rows;

use Illuminate\Support\Arr;

class ObjectRow extends Row
{

    /**
     * @param \stdClass $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $data = json_decode(json_encode($data), true);
        $this->data = Arr::dot($data);

        return $this;
    }
}
