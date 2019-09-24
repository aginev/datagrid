<?php

namespace Aginev\Datagrid\Rows;

class CollectionRow extends Row
{

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
