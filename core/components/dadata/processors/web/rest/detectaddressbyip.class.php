<?php

require_once dirname(__FILE__) . '/response.class.php';

class modDaDataDetectAddressByIpProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		return $this->dadata->detectAddress($this->dadata->getUserIp(), $this->getProperties());
	}

}

return 'modDaDataDetectAddressByIpProcessor';