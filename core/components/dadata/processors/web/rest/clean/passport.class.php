<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanPassportProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanPassport($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanPassportProcessor';