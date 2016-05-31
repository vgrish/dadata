<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanEmailProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanEmail($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanEmailProcessor';