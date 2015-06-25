<?php namespace Aginev\Datagrid\Rows;

use Aginev\Datagrid\Rows\Row;
use Aginev\Datagrid\Rows\RowInterface;

class CollectionRow extends Row {

	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function setData($data) {
		$this->data = $data;

		return $this;
	}
}