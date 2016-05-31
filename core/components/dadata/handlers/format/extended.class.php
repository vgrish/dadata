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
        foreach ($data as $key => $val) {
            if (is_numeric($key)) {
                $data[$key] = $this->processSuggest($val);
                continue;
            }
            $formatMethod = 'format' . str_replace('_', '', $prefix . $key);
            $this->dadata->showLog($formatMethod);
            if (!method_exists($this, $formatMethod)) {
                continue;
            }
            $val = $this->$formatMethod($val, $data);
            if (is_array($val)) {
                $data[$key] = $this->processSuggest($val, $prefix . $key);
            } else {
                $data[$key] = $val;
            }

            if (isset($data['suggestions'][0]['data'])) {
                $data['suggestions'][0]['data'] = array_merge(
                    $this->flattenArray($data['suggestions'][0]['data']),
                    $data['suggestions'][0]['data']
                );
            }
        }

        return $data;
    }

}