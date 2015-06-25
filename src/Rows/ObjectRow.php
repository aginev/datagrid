<?php namespace Aginev\Datagrid\Rows;

use Aginev\Datagrid\Rows\Row;
use Aginev\Datagrid\Rows\RowInterface;

class ObjectRow extends Row {

	/**
	 * @param \stdClass $data
	 *
	 * @return $this
	 */
	public function setData($data) {
		$data = json_decode(json_encode($data), true);
		$this->data = array_dot($data);

		return $this;
	}
}