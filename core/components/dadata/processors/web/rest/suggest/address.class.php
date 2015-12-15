<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataSuggestAddressProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		return $this->dadata->suggestAddress($this->getProperty('query'), $this->getProperties());
	}

}

return 'modDaDataSuggestAddressProcessor';