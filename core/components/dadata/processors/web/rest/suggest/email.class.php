<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataSuggestEmailProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		return $this->dadata->suggestEmail($this->getProperty('query'), $this->getProperties());
	}

}

return 'modDaDataSuggestEmailProcessor';