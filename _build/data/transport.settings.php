<?php

$settings = array();

$tmp = array(


    'apiToken'  => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'dadata_main',
    ),
    'apiSecret' => array(
        'value' => '',
        'xtype' => 'textfield',
        'area'  => 'dadata_main',
    ),

    //временные

//	'assets_path' => array(
//		'value' => '{base_path}dadata/assets/components/dadata/',
//		'xtype' => 'textfield',
//		'area' => 'dadata_temp',
//	),
//	'assets_url' => array(
//		'value' => '/dadata/assets/components/dadata/',
//		'xtype' => 'textfield',
//		'area' => 'dadata_temp',
//	),
//	'core_path' => array(
//		'value' => '{base_path}dadata/core/components/dadata/',
//		'xtype' => 'textfield',
//		'area' => 'dadata_temp',
//	),

    //временные

);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'dadata_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
