<?php

/**
 * The home manager controller for dadata.
 *
 */
class dadataHomeManagerController extends dadataMainController {
	/* @var dadata $dadata */
	public $dadata;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('dadata');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->dadata->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->dadata->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->dadata->config['jsUrl'] . 'mgr/misc/utils.js');
		$this->addJavascript($this->dadata->config['jsUrl'] . 'mgr/widgets/items.grid.js');
		$this->addJavascript($this->dadata->config['jsUrl'] . 'mgr/widgets/items.windows.js');
		$this->addJavascript($this->dadata->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->dadata->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "dadata-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->dadata->config['templatesPath'] . 'home.tpl';
	}
}