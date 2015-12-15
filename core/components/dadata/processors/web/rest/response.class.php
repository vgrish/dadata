<?php

abstract class modDaDataResponseProcessor extends modProcessor
{
	/** @var  dadata $dadata */
	var $dadata;

	function __construct(modX &$modx, array $properties = array())
	{
		parent::__construct($modx, $properties);

		if (!$namespace = $modx->getObject('modNamespace', 'dadata')) {
			$error = "[dadata] Not found modNamespace: dadata ";
			$this->modx->log(modX::LOG_LEVEL_ERROR, $error);
			return $this->failure($error);
		}
	}

	public function initialize()
	{
		$this->dadata = $this->modx->dadata;
		if (!is_object($this->dadata) OR !($this->dadata instanceof dadata)) {
			$corePath = $this->modx->getOption('dadata_core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/dadata/');
			$this->dadata = $this->modx->getService('dadata', 'dadata', $corePath . 'model/dadata/', $this->properties);
		}

		$propKey = $this->getProperty('propkey');
		if (empty($propKey)) {
			return $this->dadata->lexicon('err_propkey_ns');
		}

		$properties = $this->dadata->getProperties($propKey);
		if (empty($properties)) {
			return $this->dadata->lexicon('err_properties_ns');
		}

		$this->setProperties($properties);
		$this->dadata->initialize($this->getProperty('ctx', $this->modx->context->key), $this->getProperties());

		return true;
	}

}

return 'modDaDataResponseProcessor';