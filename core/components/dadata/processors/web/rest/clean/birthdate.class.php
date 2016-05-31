<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataCleanBirthdateProcessor extends modDaDataResponseProcessor
{
    function process()
    {
        return $this->dadata->cleanBirthdate($this->getProperty('query'), $this->getProperties());
    }

}

return 'modDaDataCleanBirthdateProcessor';