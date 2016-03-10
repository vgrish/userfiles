<?php

$properties = array();

$tmp = array(
    'tplForm' => array(
        'type'  => 'textfield',
        'value' => 'uf.form',
    ),
    'class'   => array(
        'type'    => 'list',
        'options' => array(
            array('text' => 'modResource', 'value' => 'modResource'),
            array('text' => 'modUser', 'value' => 'modUser'),
        ),
        'value'   => 'modResource',
    ),
    'parent'  => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'list'    => array(
        'type'  => 'textfield',
        'value' => 'default',
    ),

    'source' => array(
        'type'  => 'numberfield',
        'value' => 0,
    ),

    'active'  => array(
        'type'  => 'combo-boolean',
        'value' => true
    ),
    'anonym'  => array(
        'type'  => 'combo-boolean',
        'value' => false
    ),
    'sortby'  => array(
        'type'  => 'textfield',
        'value' => 'rank'
    ),
    'sortdir' => array(
        'type'    => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC')
        ),
        'value'   => 'ASC',
    ),

    'objectName'    => array(
        'type'  => 'textfield',
        'value' => 'UserFilesForm',
    ),
    'frontendCss'   => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]css/web/default.css',
    ),
    'frontendJs'    => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]js/web/default.js',
    ),
    'jqueryJs'      => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]vendor/jquery/jquery.min.js',
    ),
    'actionUrl'     => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]action.php',
    ),
    'toPlaceholder' => array(
        'type'  => 'textfield',
        'value' => ''
    ),
    'dropzone' => array(
        'type'  => 'textarea',
        'value' => '{"addRemoveLinks":true,"template":"base"}',
    ),

);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name'    => $k,
            'desc'    => PKG_NAME_LOWER . '_prop_' . $k,
            'lexicon' => PKG_NAME_LOWER . ':properties',
        ), $v
    );
}

return $properties;