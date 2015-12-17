<?php

/**
 * The base class for dadata.
 */
class dadata
{
	/* @var modX $modx */
	public $modx;
	/** @var string $namespace */
	public $namespace = 'dadata';
	/* @var array The array of config */
	public $config = array();
	/** @var array $initialized */
	public $initialized = array();

	/** @var $mode */
	protected $mode;
	/** @var $token */
	protected $token;
	/** @var $secret */
	protected $secret;
	/** @var $url */
	protected $url;

	/** @var Format $Format */
	public $Format;

	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array())
	{
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('dadata_core_path', $config, $this->modx->getOption('core_path') . 'components/dadata/');
		$assetsUrl = $this->modx->getOption('dadata_assets_url', $config, $this->modx->getOption('assets_url') . 'components/dadata/');
		$connectorUrl = $assetsUrl . 'connector.php';
		$assetsPath = MODX_ASSETS_PATH;

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'imagesUrl' => $assetsUrl . 'images/',
			'connectorUrl' => $connectorUrl,
			'actionUrl' => $assetsUrl . 'action.php',

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',
			'handlersPath' => $corePath . 'handlers/',

			'prepareResponse' => true,
			'jsonResponse' => true,

			'apiMode' => 0,
			'apiToken' => 0,
			'apiSecret' => 0,
			'apiUrl' => 0,
			'apiUrlFree' => 0,
			'apiUrlPay' => 0,

		), $config);

		$this->modx->addPackage('dadata', $this->config['modelPath']);
		$this->modx->lexicon->load('dadata:default');
	}


	/**
	 * @param $n
	 * @param array $p
	 */
	public function __call($n, array $p)
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
		$option = $default;
		if (!empty($key) AND is_string($key)) {
			if ($config != null AND array_key_exists($key, $config)) {
				$option = $config[$key];
			} elseif (array_key_exists($key, $this->config)) {
				$option = $this->config[$key];
			} elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
				$option = $this->modx->getOption("{$this->namespace}_{$key}");
			}
		}
		if ($skipEmpty AND empty($option)) {
			$option = $default;
		}
		return $option;
	}

	/**
	 * Initializes component into different contexts.
	 *
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array $scriptProperties
	 *
	 * @return boolean
	 */
	public function initialize($ctx = 'web', $config = array())
	{
		$this->setConfig($config);
		$this->config['ctx'] = $ctx;
		if (!empty($this->initialized[$ctx])) {
			return true;
		}

		if (!$this->Format) {
			$this->loadFormat();
		}

		switch ($ctx) {
			case 'mgr':
				break;
			default:
				if (!defined('MODX_API_MODE') OR !MODX_API_MODE) {
					$this->initialized[$ctx] = true;
				}
				break;
		}

		return true;
	}

	/** @inheritdoc} */
	public function setConfig($config = array())
	{
		$this->config = array_merge($this->config, $config);

		$this->mode = trim($this->getOption('apiMode', $this->config, $this->modx->getOption('dadata_apiMode', null, 0, true), true));
		$this->token = trim($this->getOption('apiToken', $this->config, $this->modx->getOption('dadata_apiToken', null, 0, true), true));
		$this->secret = trim($this->getOption('apiSecret', $this->config, $this->modx->getOption('dadata_apiSecret', null, 0, true), true));
		$this->url = trim($this->getOption('apiUrl', $this->config, $this->modx->getOption('dadata_apiUrl', null, 0, true), true));

		switch (true) {
			case $this->url:
				break;
			case !$this->url AND !$this->mode:
				$this->url = trim($this->getOption('apiUrlFree', $this->config, $this->modx->getOption('dadata_apiUrlFree', null, 'https://dadata.ru/api/v2', true), true));
				break;
			case !$this->url AND $this->mode:
				$this->url = trim($this->getOption('apiUrlPay', $this->config, $this->modx->getOption('dadata_apiUrlPay', null, 'https://suggestions.dadata.ru/suggestions/api/4_1/rs', true), true));
				break;
		}
	}

	/**
	 * @return bool
	 */
	public function loadFormat()
	{
		if (!is_object($this->Format) OR !($this->Format instanceof FormatInterface)) {
			$formatClass = $this->modx->loadClass('format.Format', $this->config['handlersPath'], true, true);
			if ($derivedClass = $this->modx->getOption('dadata_format_handler_class', null, '', true)) {
				if ($derivedClass = $this->modx->loadClass('format.' . $derivedClass, $this->config['handlersPath'], true, true)) {
					$formatClass = $derivedClass;
				}
			}
			if ($formatClass) {
				$this->Format = new $formatClass($this->modx, $this->config);
			}
		}
		return !empty($this->Format) AND $this->Format instanceof FormatInterface;
	}

	/**
	 * Independent registration of css and js
	 *
	 * @param string $objectName Name of object to initialize in javascript
	 */
	public function loadCustomJsCss($objectName = 'modxDaData', $configName = 'dadata')
	{
		$objectName = trim($objectName);

		$config = $this->modx->toJSON(array(
			'assetsUrl' => $this->config['assetsUrl'],
			'actionUrl' => $this->config['actionUrl'],
			'restUrl' => $this->config['actionUrl'] . '?action=rest'
		));

		$this->modx->regClientStartupScript(preg_replace('#(\n|\t)#', '', '
				<script type="text/javascript">
					modxDaDataConfig=' . $config . '
				</script>
		'), true);

		if (!isset($this->modx->loadedjscripts[$objectName])) {

			$pls = $this->makePlaceholders($this->config);
			foreach ($this->config as $k => $v) {
				if (is_string($v)) {
					$this->config[$k] = str_replace($pls['pl'], $pls['vl'], $v);
				}
			}

			$this->modx->regClientStartupScript(preg_replace('#(\n|\t)#', '', '
				<script type="text/javascript">
					' . $configName . '={};
					' . $configName . '.opts={};
				</script>
			'), true);

			if ($this->config['jqueryJs']) {
				$this->modx->regClientScript(preg_replace('#(\n|\t)#', '', '
				<script type="text/javascript">
					if (typeof jQuery == "undefined") {
						document.write("<script src=\"' . $this->config['jqueryJs'] . '\" type=\"text/javascript\"><\/script>");
					}
				</script>
				'), true);
			}

			if ($this->config['frontendCss']) {
				$this->modx->regClientCSS($this->config['frontendCss']);
			}
			if ($this->config['frontendJs']) {
				$this->modx->regClientScript($this->config['frontendJs']);
			}

			$this->modx->regClientScript("<script type=\"text/javascript\">{$objectName}.initialize({$configName}.opts);</script>", true);

		}

		return $this->modx->loadedjscripts[$objectName] = 1;
	}

	/**
	 * Shorthand for the call of processor
	 *
	 * @access public
	 *
	 * @param string $action Path to processor
	 * @param array $data Data to be transmitted to the processor
	 *
	 * @return mixed The result of the processor
	 */
	public function runProcessor($action = '', $data = array())
	{
		$this->modx->error->reset();
		$processorsPath = !empty($this->config['processorsPath']) ? $this->config['processorsPath'] : MODX_CORE_PATH;
		/* @var modProcessorResponse $response */
		$response = $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));
		return $this->config['prepareResponse'] ? $this->prepareResponse($response) : $response;
	}

	/**
	 * This method returns prepared response
	 *
	 * @param mixed $response
	 *
	 * @return array|string $response
	 */
	public function prepareResponse($response)
	{
		if ($response instanceof modProcessorResponse) {
			$output = $response->getResponse();
		} else {
			$message = $response;
			if (empty($message)) {
				$message = $this->lexicon('err_unknown');
			}
			$output = $this->failure($message);
		}
		if ($this->config['jsonResponse'] AND is_array($output)) {
			$output = $this->modx->toJSON($output);
		} elseif (!$this->config['jsonResponse'] AND !is_array($output)) {
			$output = $this->modx->fromJSON($output);
		}
		return $output;
	}

	/**
	 * return lexicon message if possibly
	 *
	 * @param $message
	 * @param array $placeholders
	 * @return string
	 */
	public function lexicon($message, $placeholders = array())
	{
		$key = '';
		if ($this->modx->lexicon->exists($message)) {
			$key = $message;
		} elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
			$key = $this->namespace . '_' . $message;
		}
		if ($key !== '') {
			$message = $this->modx->lexicon->process($key, $placeholders);
		}
		return $message;
	}

	/**
	 * @param array $request
	 * @return array
	 */
	public function getRequest($type = '', array $request = array())
	{
		return $request;
	}

	/**
	 * @param string $type
	 * @param array $opts
	 * @return array
	 */
	public function getOpts($type = '', array $opts = array())
	{
		$data = array();
		switch ($type) {
			case 'fio':
				$keys = array('query', 'count', 'parts', 'gender');
				break;
			case 'address':
				$keys = array('query', 'count', 'locations', 'from_bound', 'to_bound', 'restrict_value', 'locations_boost');
				break;
			case 'party':
				$keys = array('query', 'count', 'status', 'type', 'locations', 'locations_boost');
				break;
			case 'email':
				$keys = array('query', 'count');
				break;
			case 'bank':
				$keys = array('query', 'count', 'status', 'type');
				break;

			default:
				$keys = array();
				break;
		}

		foreach ($keys as $key) {
			if (isset($opts[$key])) {
				$data[$key] = $opts[$key];
			}
		}
		return $data;
	}


	/**
	 * @param $query
	 * @param string $type
	 * @param array $opts
	 * @return array|mixed
	 */
	public function suggestField($query, $type = '', array $opts = array())
	{
		$opts['query'] = $query;
		$this->showLog($opts);

		$cacheResponse = $this->getOption('cacheResponse', $this->config, 0, true);
		$minChars = $this->getOption('minChars', $this->config, 3, true);

		$request = $this->getRequest($type, $opts);
		$opts = $this->getOpts($type, $opts);

		if ($cacheResponse AND mb_strlen($query) > $minChars) {
			$options = array(
				'cache_key' => 'dadata/query/' . $type . '/' . sha1(serialize($opts)),
				'cacheTime' => 0,
			);
			if (!$data = $this->getCache($options)) {
				$data = $this->query('suggest/' . $type, $opts);
				$this->setCache($data, $options);
			}
			if ($this->getOption('isprocess_data', null, true, true)) {
				$data = $this->processSuggestData($data, $request);
			}
		} else {
			$data = $this->query('suggest/' . $type, $opts);
			if ($this->getOption('isprocess_data', null, true, true)) {
				$data = $this->processSuggestData($data, $request);
			}
		}

		$this->showLog($data);
		return isset($data['suggestions']) ? $data : array();
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function processSuggestData(array $data = array(), array $request = array())
	{
		if (!$this->Format) {
			$this->loadFormat();
		}
		return $this->Format->processSuggestData($data, $request);
	}

	/**
	 * @param $name
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093386
	 *
	 * count - Количество возвращаемых подсказок (по умолчанию — 10).
	 * parts - Подсказки по части ФИО массив (NAME / PATRONYMIC / SURNAME )
	 * gender - Пол (UNKNOWN / MALE / FEMALE)
	 *
	 * @return array
	 */
	public function suggestName($name, array $opts = array())
	{
		return $this->suggestField($name, 'fio', $opts);
	}

	/**
	 * @param $address
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093376
	 *
	 * count - Количество возвращаемых подсказок (по умолчанию — 10).
	 * locations - Ограничение области поиска массив.
	 *        Каждый объект-ограничение в параметре locations может содержать поля
	 *        kladr_id, postal_code, region, area, city, settlement или street.
	 *
	 * @return array
	 */
	public function suggestAddress($address, array $opts = array())
	{
		return $this->suggestField($address, 'address', $opts);
	}

	/**
	 * @param $party
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093392
	 *
	 * count - Количество возвращаемых подсказок (по умолчанию — 10).
	 * status - Ограничение по статусу организации массив. (ACTIVE / LIQUIDATING / LIQUIDATED )
	 * type - Ограничение по типу организации массив. (LEGAL / INDIVIDUAL )
	 * locations - Ограничение по региону массив. (kladr_id )
	 *
	 * @return array
	 */
	public function suggestParty($party, array $opts = array())
	{
		return $this->suggestField($party, 'party', $opts);
	}

	/**
	 * @param $email
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093400
	 *
	 * count - Количество возвращаемых подсказок (по умолчанию — 10).
	 *
	 * @return array
	 */
	public function suggestEmail($email, array $opts = array())
	{
		return $this->suggestField($email, 'email', $opts);
	}

	/**
	 * @param $bank
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093406
	 *
	 * count - Количество возвращаемых подсказок (по умолчанию — 10).
	 * status - Ограничение по статусу организации массив. (ACTIVE / LIQUIDATING / LIQUIDATED )
	 * type - Ограничение по типу организации массив. (LEGAL / INDIVIDUAL )
	 *
	 * @return array
	 */
	public function suggestBank($bank, array $opts = array())
	{
		return $this->suggestField($bank, 'bank', $opts);
	}

	/**
	 * @param $fias
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093381
	 *
	 * @return array
	 */
	public function suggestFias($fias, array $opts = array())
	{
		$opts['query'] = $fias;
		$data = $this->query('findById/address', array());
		return isset($data['suggestions']) ? $data : array();
	}

	/**
	 * @param $field
	 * @param string $type
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanField($field, $type = '')
	{
		$opts = array($field);
		$data = $this->query('clean/' . $type, $opts);
		return isset($data[0]) ? $data[0] : array();
	}

	/**
	 * @param $address
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanAddresses($address)
	{
		return $this->cleanField($address, 'address');
	}

	/**
	 * @param $phone
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanPhone($phone)
	{
		return $this->cleanField($phone, 'phone');
	}

	/**
	 * @param $passport
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanPassport($passport)
	{
		return $this->cleanField($passport, 'passport');
	}

	/**
	 * @param $name
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanName($name)
	{
		return $this->cleanField($name, 'name');
	}

	/**
	 * @param $email
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanEmail($email)
	{
		return $this->cleanField($email, 'email');
	}

	/**
	 * @param $birthdate
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanBirthdate($birthdate)
	{
		return $this->cleanField($birthdate, 'birthdate');
	}

	/**
	 * @param $vehicle
	 *
	 * https://dadata.ru/api/clean/
	 *
	 * @return array
	 */
	public function cleanVehicle($vehicle)
	{
		return $this->cleanField($vehicle, 'vehicle');
	}

	/**
	 * @param array $structure
	 * @param array $data
	 *
	 * https://dadata.ru/api/clean/#response-record
	 *
	 * @return array
	 */
	public function cleanStructure(array $structure = array(), array $data = array())
	{
		$opts = array(
			'structure' => $structure,
			'data' => $data
		);
		$data = $this->query('clean', $opts);
		return isset($data['structure']) ? $data : array();
	}

	/**
	 * @param string $ip
	 * @param array $opts
	 *
	 * https://confluence.hflabs.ru/pages/viewpage.action?pageId=350093389
	 *
	 * @return array
	 */
	public function detectAddress($ip = '', array $opts = array())
	{
		$data = $this->query('detectAddressByIp?ip=' . $ip, $opts, false);
		return isset($data['location']) ? $data : array();
	}

	/**
	 * @param $query
	 * @param string $type
	 * @param array $opts
	 * @return array|mixed
	 */
	public function suggestStatus($type = '', array $opts = array())
	{
		$this->showLog($opts);
		if (!empty($type)) {
			$type = '/' . $type;
		}
		$data = $this->query('status' . $type, array(), false);
		return (count($data) > 0) ? $data : array();
	}

	/**
	 * @param string $type
	 * @param array $opts
	 * @return array|mixed
	 */
	public function profileBalance()
	{
		$data = $this->query('profile/balance', array(), false, array("X-Secret: {$this->secret}"));
		return (count($data) > 0) ? $data : array();
	}


	/**
	 * @param $url
	 * @param $data
	 * @param bool $post
	 * @return mixed
	 * @throws ErrorException
	 */
	public function query($url, $data, $post = true, array $headers = array())
	{
		$response = $this->request($this->url . '/' . $url, $data, $post, $headers);
		return $response;
	}

	/**
	 * @param $url
	 * @param array $data
	 * @param bool $isPost
	 * @return mixed
	 * @throws ErrorException
	 */
	protected function request($url, array $data = array(), $isPost = true, array $headers = array())
	{
		if ($isPost) {
			$headers = array_merge($headers, array(
				"Content-Type: application/json",
				"Authorization: Token {$this->token}",
				"X-Secret: {$this->secret}",
			));
			$opts = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true,
				CURLOPT_HEADER => false,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => json_encode($data),
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => $headers
			);
		} else {
			$headers = array_merge($headers, array(
				"Accept: application/json",
				"Authorization: Token {$this->token}"
			));
			$opts = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true,
				CURLOPT_HEADER => false,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_POST => false,
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => $headers
			);
		}

		$curl = curl_init();
		curl_setopt_array($curl, $opts);

		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			throw new ErrorException(curl_error($curl), curl_errno($curl));
		}
		curl_close($curl);

		$response = json_decode($response, true);
		$jsonError = json_last_error();

		if (is_null($response) AND ($jsonError != JSON_ERROR_NONE)) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, "[dadata] JSON Error: " . $jsonError . "\n" . $response);
		}

		return $response;
	}

	/** @inheritdoc} */
	public function getPropertiesKey(array $properties = array())
	{
		return !empty($properties['propkey']) ? $properties['propkey'] : false;
	}

	/** @inheritdoc} */
	public function saveProperties(array $properties = array())
	{
		return !empty($properties['propkey']) ? $_SESSION[$this->namespace][$properties['propkey']] = $properties : false;
	}

	/** @inheritdoc} */
	public function getProperties($key = '')
	{
		return !empty($_SESSION[$this->namespace][$key]) ? $_SESSION[$this->namespace][$key] : array();
	}

	/** @inheritdoc} */
	public function clearJson($string)
	{
		$string = preg_replace("#[\']+#si", '"', preg_replace("#[\r\n\t\s]+#si", '', $string));
		return $string;
	}

	/** @inheritdoc} */
	public function fromJson($string)
	{
		$string = $this->clearJson($string);
		$json = json_decode($string, true);
		$jsonError = json_last_error();
		if ($jsonError != JSON_ERROR_NONE) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, "[dadata] JSON Error: " . $jsonError . "\n" . $string);
		}
		return $json;
	}

	/**
	 * @return string
	 */
	public static function getUserIp()
	{
		$ip = '127.0.0.1';

		switch (true) {
			case (isset($_SERVER['HTTP_CLIENT_IP']) AND $_SERVER['HTTP_CLIENT_IP'] != ''):
				$ip = $_SERVER['HTTP_CLIENT_IP'];
				break;
			case (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND $_SERVER['HTTP_X_FORWARDED_FOR'] != ''):
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				break;
			case (isset($_SERVER['REMOTE_ADDR']) AND $_SERVER['REMOTE_ADDR'] != ''):
				$ip = $_SERVER['REMOTE_ADDR'];
				break;
		}

		return $ip;
	}

	/**
	 * from https://github.com/bezumkin/pdoTools/blob/f947b2abd9511919de56cbb85682e5d0ef52ebf4/core/components/pdotools/model/pdotools/pdotools.class.php#L282
	 *
	 * Transform array to placeholders
	 *
	 * @param array $array
	 * @param string $plPrefix
	 * @param string $prefix
	 * @param string $suffix
	 * @param bool $uncacheable
	 * @return array
	 */
	public function makePlaceholders(array $array = array(), $plPrefix = '', $prefix = '[[+', $suffix = ']]', $uncacheable = true)
	{
		$result = array('pl' => array(), 'vl' => array());
		$uncached_prefix = str_replace('[[', '[[!', $prefix);
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$result = array_merge_recursive($result, $this->makePlaceholders($v, $plPrefix . $k . '.', $prefix, $suffix, $uncacheable));
			} else {
				$pl = $plPrefix . $k;
				$result['pl'][$pl] = $prefix . $pl . $suffix;
				$result['vl'][$pl] = $v;
				if ($uncacheable) {
					$result['pl']['!' . $pl] = $uncached_prefix . $pl . $suffix;
					$result['vl']['!' . $pl] = $v;
				}
			}
		}
		return $result;
	}

	/**
	 * Sets data to cache
	 *
	 * @param mixed $data
	 * @param mixed $options
	 *
	 * @return string $cacheKey
	 */
	public function setCache($data = array(), $options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		if (!empty($cacheKey) AND !empty($cacheOptions) AND $this->modx->getCacheManager()) {
			$this->modx->cacheManager->set(
				$cacheKey,
				$data,
				$cacheOptions[xPDO::OPT_CACHE_EXPIRES],
				$cacheOptions
			);
		}
		return $cacheKey;
	}

	/**
	 * Returns data from cache
	 *
	 * @param mixed $options
	 *
	 * @return mixed
	 */
	public function getCache($options = array())
	{
		$cacheKey = $this->getCacheKey($options);
		$cacheOptions = $this->getCacheOptions($options);
		$cached = '';
		if (!empty($cacheOptions) AND !empty($cacheKey) AND $this->modx->getCacheManager()) {
			$cached = $this->modx->cacheManager->get($cacheKey, $cacheOptions);
		}
		return $cached;
	}

	/**
	 * Returns array with options for cache
	 *
	 * @param $options
	 *
	 * @return array
	 */
	protected function getCacheOptions($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		$cacheOptions = array(
			xPDO::OPT_CACHE_KEY => !empty($options['cache_key'])
				? 'default'
				: $this->modx->getOption('cache_resource_key', null, 'default'),
			xPDO::OPT_CACHE_HANDLER => !empty($options['cache_handler'])
				? $options['cache_handler']
				: $this->modx->getOption('cache_resource_handler', null, 'xPDOFileCache'),
			xPDO::OPT_CACHE_EXPIRES => $options['cacheTime'] !== ''
				? (integer)$options['cacheTime']
				: (integer)$this->modx->getOption('cache_resource_expires', null, 0),
		);
		return $cacheOptions;
	}

	/**
	 * Returns key for cache of specified options
	 *
	 * @var mixed $options
	 *
	 * @return bool|string
	 */
	protected function getCacheKey($options = array())
	{
		if (empty($options)) {
			$options = $this->config;
		}
		if (!empty($options['cache_key'])) {
			return $options['cache_key'];
		}
		$key = !empty($this->modx->resource) ? $this->modx->resource->getCacheKey() : '';
		return $key . '/' . sha1(serialize($options));
	}

	/**
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 * @return array|string
	 */
	public function failure($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => false,
			'message' => $this->lexicon($message, $placeholders),
			'data' => $data,
		);
		return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
	}

	/**
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array())
	{
		$response = array(
			'success' => true,
			'message' => $this->lexicon($message, $placeholders),
			'data' => $data,
		);
		return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
	}

	/**
	 * @param string $message
	 * @param bool $show
	 * @return bool
	 */
	public function showLog($message = '', $show = false)
	{
		if (($show OR !empty($this->config['showLog']))) {
			$this->modx->log(modX::LOG_LEVEL_ERROR, print_r('[' . $this->namespace . '] ' . (($show) ? 'show' : ''), 1));
			$this->modx->log(modX::LOG_LEVEL_ERROR, print_r($message, 1));
		}
		return true;
	}

}