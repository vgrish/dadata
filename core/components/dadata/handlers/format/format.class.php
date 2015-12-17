<?php

interface FormatInterface
{
	/** @inheritdoc} */
	public function processSuggestData(array $data = array());
}

class Format implements FormatInterface
{

	/** @var modX $modx */
	protected $modx;
	/** @var dadata $dadata */
	protected $dadata;
	/** @var array $config */
	protected $config = array();
	/** @var string $namespace */
	protected $namespace;
	/** @var string $namespace */
	protected $request;

	/**
	 * @param $modx
	 * @param $config
	 */
	public function __construct($modx, &$config)
	{
		$this->modx = $modx;
		$this->config =& $config;

		$corePath = $this->modx->getOption('dadata_core_path', null, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/dadata/');
		/** @var dadata $dadata */
		$this->dadata = &$this->modx->getService(
			'dadata',
			'dadata',
			$corePath . 'model/dadata/',
			array(
				'core_path' => $corePath
			)
		);

		$this->namespace = $this->dadata->namespace;
	}


	/**
	 * @param $n
	 * @param array $p
	 */
	public function __call($n, array$p)
	{
		echo __METHOD__ . ' says: ' . $n;
	}

	/**
	 * @param $key
	 * @param array $config
	 * @param null $default
	 * @return mixed|null
	 */
	public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
	{
		return $this->dadata->getOption($key, $config, $default, $skipEmpty);
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function processSuggestData(array $data = array(), array $request = array())
	{
		$this->request = $request;

		while (list($key, $val) = each($data)) {
			if (!is_string($val) AND !is_array($val)) {
				continue;
			}
			if (is_numeric($key)) {
				$data[$key] = $this->processSuggestData($val);
			}
			$formatMethod = 'format' . ucfirst(str_replace('_', '', $key));
			$this->dadata->showLog($formatMethod);
			if (!method_exists($this, $formatMethod)) {
				continue;
			}
			$val = $this->$formatMethod($val);
			if (is_array($val)) {
				$data[$key] = $this->processSuggestData($val);
			} else {
				$data[$key] = $val;
			}
		}
		return $data;
	}


	/**
	 * @param array $data
	 * @return array
	 */
	public function formatSuggestions(array $data = array())
	{
		$return = $this->getOption('return', $this->request, false, true);
		if (!$return) {
			return $data;
		}

		$keys = $this->getOption('keys', $this->request['return'], false, true);
		if (!$keys) {
			return $data;
		}

		$delimiter = $this->getOption('delimiter', $this->request['return'], ' ');

		foreach ($data as $k => $v) {
			$value = array();
			foreach ($keys as $key) {
				if (isset($v['data'][$key])) {
					$value[] = $v['data'][$key];
				}
				$data[$k]['return'] = implode($delimiter, $value);;
			}
		}
		return $data;
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public function formatValue($value = '')
	{
		return $value;
	}

}