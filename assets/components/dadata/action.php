<?php

if (empty($_REQUEST['action'])) {
	@session_write_close();
	die('Access denied');
}
$_REQUEST['action'] = strtolower(ltrim($_REQUEST['action'], '/'));
define('MODX_API_MODE', true);
$productionIndex = dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$developmentIndex = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
if (file_exists($productionIndex)) {
	/** @noinspection PhpIncludeInspection */
	require_once $productionIndex;
} else {
	/** @noinspection PhpIncludeInspection */
	require_once $developmentIndex;
}
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;
$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {
	$modx->switchContext($ctx);
	$modx->user = null;
	$modx->getUser($ctx);
}

/** @var  $properties */
$properties = $_REQUEST;
/** @var  $requestPayload */
$requestPayload = json_decode(file_get_contents('php://input'), true);
$jsonError = json_last_error();
if ($jsonError != JSON_ERROR_NONE) {
	$modx->log(modX::LOG_LEVEL_ERROR, "[dadata] JSON Error: " . $jsonError);
} elseif (!empty($requestPayload)) {
	$properties = array_merge($properties, $requestPayload);
}
/** @var  $propKey */

switch (true) {
	case !empty($_SERVER['HTTP_AUTHORIZATION']):
		$properties['propkey'] = trim(str_replace('Token', '', $_SERVER['HTTP_AUTHORIZATION']));
		break;
	case !empty($_SERVER['HTTP_CGI_AUTHORIZATION']):
		$properties['propkey'] = trim(str_replace('Token', '', $_SERVER['HTTP_CGI_AUTHORIZATION']));
		break;
}

define('MODX_ACTION_MODE', true);
/* @var dadata $dadata */
$corePath = $modx->getOption('dadata_core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/dadata/');
$dadata = $modx->getService('dadata', 'dadata', $corePath . 'model/dadata/', array('core_path' => $corePath));
if ($modx->error->hasError() OR !($dadata instanceof dadata)) {
	@session_write_close();
	die('Error');
}
$dadata->initialize($ctx);
$dadata->config['processorsPath'] = $dadata->config['processorsPath'] . 'web/';
if (!$response = $dadata->runProcessor($_REQUEST['action'], $properties)) {
	$response = $modx->toJSON(array(
		'success' => false,
		'code' => 401,
	));
}
@session_write_close();
echo $response;