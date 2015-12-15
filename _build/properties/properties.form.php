<?php

$properties = array();

$tmp = array(

	'selector' => array(
		'type' => 'textfield',
		'value' => '#dadata-form',
	),
	'suggestions' => array(
		'type' => 'textarea',
		'value' => '',
	),

//	'configName' => array(
//		'type' => 'textfield',
//		'value' => 'dadata',
//	),

	'apiMode' => array(
		'type' => 'list',
		'options' => array(
			array('text' => 'free', 'value' => 0),
			array('text' => 'pay', 'value' => 1),
		),
		'value' => 0,
	),

	'apiToken' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'apiSecret' => array(
		'type' => 'textfield',
		'value' => '',
	),


//	'objectName' => array(
//		'type' => 'textfield',
//		'value' => 'modxDaData',
//	),
	'jqueryJs' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]vendor/jquery/jquery.min.js',
	),

	'frontendCss' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]css/web/default.css',
	),
	'frontendJs' => array(
		'type' => 'textfield',
		'value' => '[[+assetsUrl]]js/web/default.js',
	),
//	'showLog' => array(
//		'type' => 'combo-boolean',
//		'value' => false,
//	),
//	'cacheResponse' => array(
//		'type' => 'combo-boolean',
//		'value' => true,
//	),

);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(
		array(
			'name' => $k,
			'desc' => PKG_NAME_LOWER . '_prop_' . $k,
			'lexicon' => PKG_NAME_LOWER . ':properties',
		), $v
	);
}

return $properties;