<?php

if (!class_exists('Format')) {
    require_once dirname(__FILE__) . '/format.class.php';
}

class Extended extends Format
{

    /**
     * @param array  $data
     * @param string $prefix
     *
     * @return array
     */
    public function processSuggest(array $data = array(), $prefix = '')
    {
        foreach ($data as $key => &$val) {
            if (is_numeric($key)) {
                $val = $this->processSuggest($val);
                continue;
            }

            $formatMethod = 'format' . str_replace('_', '', $prefix . $key);

            $this->dadata->showLog($formatMethod);
            if (!method_exists($this, $formatMethod)) {
                continue;
            }

            $val = $this->$formatMethod($val, $data);
            if (is_array($val)) {
                $val = $this->processSuggest($val, $prefix . $key);
            }
        }

        return $data;
    }

    public function formatData($val = '', array $data = array())
    {
        $val = array_merge($this->flattenArray($val), $val);

        return $val;
    }

}