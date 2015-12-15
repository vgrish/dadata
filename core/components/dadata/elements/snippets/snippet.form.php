<?php

/** @var array $scriptProperties */
/** @var dadata $dadata */
if (!$dadata = $modx->getService('dadata', 'dadata', $modx->getOption('dadata_core_path', null, $modx->getOption('core_path') . 'components/dadata/') . 'model/dadata/', $scriptProperties)) {
	return 'Could not load dadata class!';
}

/* clear $scriptProperties */
foreach (array('suggestions') as $k) {
	$scriptProperties[$k] = $dadata->fromJson($scriptProperties[$k]);
}

$context = $scriptProperties['context'] = $modx->getOption('context', $scriptProperties, $modx->context->key, true);
$selector = $scriptProperties['selector'] = $modx->getOption('selector', $scriptProperties, '#dadata-form', true);
$suggestions = $scriptProperties['suggestions'] = $modx->getOption('suggestions', $scriptProperties, '{}', true);
$apiMode = $scriptProperties['apiMode'] = $modx->getOption('apiMode', $scriptProperties, 0, true);
$apiToken = $scriptProperties['apiToken'] = $modx->getOption('apiToken', $scriptProperties, 0, true);
$apiSecret = $scriptProperties['apiSecret'] = $modx->getOption('apiSecret', $scriptProperties, 0, true);
$objectName = $scriptProperties['objectName'] = $modx->getOption('objectName', $scriptProperties, 'modxDaData', true);
$configName = $scriptProperties['configName'] = $modx->getOption('configName', $scriptProperties, 'dadata', true);
$showLog = $scriptProperties['showLog'] = $modx->getOption('showLog', $scriptProperties, false, true);
$cacheResponse = $scriptProperties['cacheResponse'] = $modx->getOption('cacheResponse', $scriptProperties, true, true);
$snippetName = $scriptProperties['snippetName'] = $modx->getOption('snippetName', $scriptProperties, $this->get('name'), true);

$propkey = array();
foreach ($scriptProperties as $k => $v) {
	if (!in_array($k, array('id', 'user_id'))) {
		$propkey[$k] = $v;
	}
}
$propkey = $scriptProperties['propkey'] = $modx->getOption('propkey', $scriptProperties, sha1(serialize($propkey)), true);

$dadata->initialize($context, $scriptProperties);
$dadata->saveProperties($scriptProperties);
$dadata->loadCustomJsCss($objectName, $configName);

$opts = $modx->toJSON(array(
	'idx' => "{$configName}.opts.idx{$propkey}",
	'propkey' => $propkey,
	'selector' => $selector,
	'suggestions' => $suggestions
));

$modx->regClientScript("<script type=\"text/javascript\">{$configName}.opts.idx{$propkey}={$opts};</script>", true);

return '';
