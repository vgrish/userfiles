<?php

$properties = array();

$tmp = array(
    'product'       => array(
        'type'  => 'numberfield',
        'value' => '',
    ),
    'tpl'           => array(
        'type'  => 'textfield',
        'value' => 'tpl.msGallery',
    ),
    'limit'         => array(
        'type'  => 'numberfield',
        'value' => 0,
    ),
    'offset'        => array(
        'type'  => 'numberfield',
        'value' => 0,
    ),
    'sortby'        => array(
        'type'  => 'textfield',
        'value' => 'rank',
    ),
    'sortdir'       => array(
        'type'    => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC'),
        ),
        'value'   => 'ASC',
    ),
    'toPlaceholder' => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'showLog'       => array(
        'type'  => 'combo-boolean',
        'value' => false,
    ),
    'where'         => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'includeThumbs' => array(
        'type'  => 'textfield',
        'value' => '120x90',
    ),
    'mime'          => array(
        'type'  => 'image%',
        'value' => '',
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