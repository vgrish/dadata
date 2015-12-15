<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataSuggestFioProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		return $this->dadata->suggestName($this->getProperty('query'), $this->getProperties());
	}

}

return 'modDaDataSuggestFioProcessor';