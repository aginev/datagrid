<?php namespace Aginev\Datagrid\Rows;

use Aginev\Datagrid\Rows\Row;
use Aginev\Datagrid\Rows\RowInterface;
use Illuminate\Support\Collection;

class ArrayRow extends Row {

	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function setData($data) {
		$this->data = array_dot($data);

		return $this;
	}
}