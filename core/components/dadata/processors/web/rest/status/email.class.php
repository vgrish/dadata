<?php

require_once dirname(dirname(__FILE__)) . '/response.class.php';

class modDaDataStatusEmailProcessor extends modDaDataResponseProcessor
{
	function process()
	{
		$type = end(explode('/', $this->getProperty('action')));
		return $this->dadata->suggestStatus($type, $this->getProperties());
	}

}

return 'modDaDataStatusEmailProcessor';