<?php

$settings = array();

$tmp = array(


    'working_templates'       => array(
        'value' => '1,2,3',
        'xtype' => 'textfield',
        'area'  => 'userfiles_main',
    ),
    'source_default'          => array(
        'value' => '0',
        'xtype' => 'modx-combo-source',
        'area'  => 'userfiles_main',
    ),
    'duplicate_search_fields' => array(
        'value' => 'parent,class,list,hash,source',
        'xtype' => 'textarea',
        'area'  => 'userfiles_main',
    ),
   /* 'salt' => array(
        'value' => '12345678',
        'xtype' => 'textfield',
        'area'  => 'userfiles_main',
    ),*/


    'phpThumb_config_max_source_pixels' => array(
        'value' => '90000000',
        'xtype' => 'textfield',
        'area'  => 'userfiles_phpThumb',
    ),
    'phpThumb_config_cache_directory'   => array(
        'value' => '{core_path}cache/phpthumb/',
        'xtype' => 'textfield',
        'area'  => 'userfiles_phpThumb',
    ),


    'chunk_link_file'  => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area'  => 'userfiles_links',
    ),
    'chunk_link_image' => array(
        'value' => '',
        'xtype' => 'numberfield',
        'area'  => 'userfiles_links',
    ),


    //временные
/*
        'assets_path' => array(
            'value' => '{base_path}userfiles/assets/components/userfiles/',
            'xtype' => 'textfield',
            'area'  => 'userfiles_temp',
        ),
        'assets_url'  => array(
            'value' => '/userfiles/assets/components/userfiles/',
            'xtype' => 'textfield',
            'area'  => 'userfiles_temp',
        ),
        'core_path'   => array(
            'value' => '{base_path}userfiles/core/components/userfiles/',
            'xtype' => 'textfield',
            'area'  => 'userfiles_temp',
        ),*/


    //временные
);

foreach ($tmp as $k => $v) {
    /* @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key'       => 'userfiles_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}

unset($tmp);
return $settings;
