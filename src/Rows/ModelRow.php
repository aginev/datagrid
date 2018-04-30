<?php namespace Aginev\Datagrid\Rows;

use Aginev\Datagrid\Rows\Row;
use Aginev\Datagrid\Rows\RowInterface;

class ModelRow extends Row {

	public function __get($key) {
		// Get the keys separated by .
		$keys = explode('.', $key);

		$value = $this->getData();

		// The easiest way to chain the object properties
		foreach ($keys as $key) {
			$value = $value->{$key};
		}

		return $value;
	}

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