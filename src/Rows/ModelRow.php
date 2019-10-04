<?php

namespace Aginev\Datagrid\Rows;

class ModelRow extends Row
{

    public function __get($key)
    {
        // Get the keys separated by .
        $keys = explode('.', $key);

        $value = $this->getData();

        // The easiest way to chain the object properties
        foreach ($keys as $key) {
            try {
                $value = $value->{$key};
            } catch (\Exception $e) {
                $value = '';
                break;
            }
        }

        return $value;
    }

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
