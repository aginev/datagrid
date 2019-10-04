<?php

namespace Aginev\Datagrid\Rows;


interface RowInterface
{

    public function __get($key);

    public function setData($data);

}
