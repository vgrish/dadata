<?php

/**
 * Class dadataMainController
 */
abstract class dadataMainController extends modExtraManagerController {
	/** @var dadata $dadata */
	public $dadata;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('dadata_core_path', null, $this->modx->getOption('core_path') . 'components/dadata/');
		require_once $corePath . 'model/dadata/dadata.class.php';

		$this->dadata = new dadata($this->modx);
		$this->addCss($this->dadata->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->dadata->config['jsUrl'] . 'mgr/dadata.js');
		$this->addHtml('
		<script type="text/javascript">
			dadata.config = ' . $this->modx->toJSON($this->dadata->config) . ';
			dadata.config.connector_url = "' . $this->dadata->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('dadata:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends dadataMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}