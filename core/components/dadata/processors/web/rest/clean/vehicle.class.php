<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanVehicleProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanVehicle($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanVehicleProcessor';