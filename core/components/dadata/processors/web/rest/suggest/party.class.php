<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataSuggestPartyProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		return $this->dadata->suggestParty($this->getProperty('query'), $this->getProperties());
	}

}

return 'modDaDataSuggestPartyProcessor';