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

		$this->mode = trim($this->getOption('apiMode', $this->config, 0, true));
		$this->token = trim($this->getOption('apiToken', $this->config, 0, true));
		$this->secret = trim($this->getOption('apiSecret', $this->config, 0, true));
		$this->url = trim($this->getOption('apiUrl', $this->config, 0, true));

		switch (true) {
			case $this->url:
				break;
			case !$this->url AND !$this->mode:
				$this->url = trim($this->getOption('apiUrlFree', $this->config, 'https://dadata.ru/api/v2', true));
				break;
			case !$this->url AND $this->mode:
				$this->url = trim($this->getOption('apiUrlPay', $this->config, 'https://suggestions.dadata.ru/suggestions/api/4_1/rs', true));
				break;
		}

		//$this->modx->log(1, print_r($this->config, 1));
//
//		$this->modx->log(1, print_r($this->type,1));
//		$this->modx->log(1, print_r($this->mode,1));
//		$this->modx->log(1, print_r($this->token,1));
//		$this->modx->log(1, print_r($this->secret,1));
//		$this->modx->log(1, print_r($this->url,1));
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
		$opts['query'] = $name;
		$data = $this->query('suggest/fio', $opts);
		return isset($data['suggestions']) ? $data['suggestions'] : array();
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
		$opts['query'] = $address;
		$data = $this->query('suggest/address', $opts);
		return isset($data['suggestions']) ? $data['suggestions'] : array();
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
		$data = $this->query('findById/address', $opts);
		return isset($data['suggestions']) ? $data['suggestions'] : array();
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
		$opts['query'] = $party;
		$data = $this->query('suggest/party', $opts);
		return isset($data['suggestions']) ? $data['suggestions'] : array();
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
		$opts['query'] = $email;
		$data = $this->query('suggest/email', $opts);
		return isset($data['suggestions']) ? $data['suggestions'] : array();
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
		$opts['query'] = $bank;
		$data = $this->query('suggest/bank', $opts);
		return isset($data['suggestions']) ? $data['suggestions'] : array();
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
		return isset($data['location']) ? $data['location'] : array();
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
		$data = $this->query('clean/'. $type, $opts);
		return isset($data[0]) ? $data[0] : array();
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
	 * @param $url
	 * @param $data
	 * @param bool $post
	 * @return mixed
	 * @throws ErrorException
	 */
	public function query($url, $data, $post = true)
	{
		$response = $this->request($this->url . '/' . $url, $data, $post);
		return $response;
	}

	protected function request($url, array $data = array(), $isPost = true)
	{
		if ($isPost) {
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
				CURLOPT_HTTPHEADER => array(
					"Content-Type: application/json",
					"Authorization: Token {$this->token}",
					"X-Secret: {$this->secret}",
				)
			);
		} else {
			$opts = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLINFO_HEADER_OUT => true,
				CURLOPT_VERBOSE => true,
				CURLOPT_HEADER => false,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 5,
				CURLOPT_POST => false,
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => array(
					"Accept: application/json",
					"Authorization: Token {$this->token}",
					//"X-Secret: {$this->secret}",
				)
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
			$this->modx->log(modX::LOG_LEVEL_ERROR, "[dadata] JSON Error: " . json_last_error_msg() . "Error code: " . $jsonError);
		}

		return $response;
	}

}