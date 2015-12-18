<?php

interface FormatInterface
{
	/** @inheritdoc} */
	public function processSuggestData(array $suggest = array(), array $request = array());
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
	public function processSuggestData(array $suggest = array(), array $request = array())
	{
		$this->request = $request;
		return $this->processSuggest($suggest);
	}

	/**
	 * @param array $data
	 * @param string $prefix
	 * @return array
	 */
	public function processSuggest(array $data = array(), $prefix = '')
	{
		foreach ($data as $key => $val) {
			if (is_numeric($key)) {
				$data[$key] = $this->processSuggest($val);
				continue;
			}
			$formatMethod = 'format' . str_replace('_', '', $prefix . $key);
			$this->dadata->showLog($formatMethod);
			if (!method_exists($this, $formatMethod)) {
				continue;
			}
			$val = $this->$formatMethod($val, $data);
			if (is_array($val)) {
				$data[$key] = $this->processSuggest($val, $prefix . $key);
			} else {
				$data[$key] = $val;
			}

		}
		return $data;
	}


	/**
	 * @param string $val
	 * @param array $data
	 * @return mixed|string
	 */
	public function formatReturn($val = '', array $data = array())
	{
		$keys = $this->getOption('keys', $this->request['return'], false, true);
		if (!$keys) {
			return $val;
		}
		$properties = $this->flattenArray($data);
		$pls = $this->makePlaceholders($properties);
		$delimiter = $this->getOption('delimiter', $this->request['return'], ' ');
		$val = str_replace($pls['pl'], $pls['vl'], implode($delimiter, $keys));
		return $val;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public function formatSuggestions($val = '', array $data = array())
	{
		$return = $this->getOption('return', $this->request, false, true);
		if (!$return) {
			return $val;
		}

		if (is_array($val)) {
			foreach ($val as $k => $v) {
				$val[$k]['return'] = null;
			}
		}

		return $val;
	}

	/**
	 * @param array $array
	 * @param string $plPrefix
	 * @param string $prefix
	 * @param string $suffix
	 * @param bool $uncacheable
	 * @return array
	 */
	public function makePlaceholders(array $array = array(), $plPrefix = '', $prefix = '', $suffix = '', $uncacheable = true)
	{
		return $this->dadata->makePlaceholders($array, $plPrefix, $prefix, $suffix, $uncacheable);
	}

	/**
	 * @param array $array
	 * @param string $prefix
	 * @return array
	 */
	public function flattenArray(array $array = array(), $prefix = '')
	{
		$outArray = array();
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
			} else {
				$outArray[$prefix . $key] = $value;
			}
		}
		return $outArray;
	}

}