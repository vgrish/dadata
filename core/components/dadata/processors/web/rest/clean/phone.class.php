<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanPhoneProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanPhone($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanPhoneProcessor';