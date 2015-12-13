<?php

/**
 * Create an Item
 */
class dadataItemCreateProcessor extends modObjectCreateProcessor {
	public $objectType = 'dadataItem';
	public $classKey = 'dadataItem';
	public $languageTopics = array('dadata');
	//public $permission = 'create';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$name = trim($this->getProperty('name'));
		if (empty($name)) {
			$this->modx->error->addField('name', $this->modx->lexicon('dadata_item_err_name'));
		}
		elseif ($this->modx->getCount($this->classKey, array('name' => $name))) {
			$this->modx->error->addField('name', $this->modx->lexicon('dadata_item_err_ae'));
		}

		return parent::beforeSet();
	}

}

return 'dadataItemCreateProcessor';