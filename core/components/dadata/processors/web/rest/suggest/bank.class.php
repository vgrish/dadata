<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataSuggestBankProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		return $this->dadata->suggestBank($this->getProperty('query'), $this->getProperties());
	}

}

return 'modDaDataSuggestBankProcessor';