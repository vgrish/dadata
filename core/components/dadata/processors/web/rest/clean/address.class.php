<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanAddressProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanAddresses($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanAddressProcessor';