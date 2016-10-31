<?php

$properties = array();

$tmp = array(
    'tpl'            => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'class'          => array(
        'type'  => 'textfield',
        'value' => 'modResource',
    ),
    'leftJoinFiles'   => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'innerJoinFiles'  => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'whereFiles'      => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'includeFilesThumbs'   => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'includeAllFiles' => array(
        'type' => 'combo-boolean',
        'value' => false,
    ),
    'element'        => array(
        'type'  => 'textfield',
        'value' => 'pdoResources',
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