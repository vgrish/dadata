<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanNameProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanName($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanNameProcessor';