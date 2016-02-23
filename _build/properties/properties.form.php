<?php

$properties = array();

$tmp = array(
    'tplForm' => array(
        'type'  => 'textfield',
        'value' => 'uf.form',
    ),

    'class'       => array(
        'type'    => 'list',
        'options' => array(
            array('text' => 'modResource', 'value' => 'modResource'),
            array('text' => 'modUser', 'value' => 'modUser'),
        ),
        'value'   => 'modResource',
    ),
    'parent'      => array(
        'type'  => 'textfield',
        'value' => '',
    ),
    'list'        => array(
        'type'  => 'textfield',
        'value' => 'default',
    ),
    /* 'listMaxCount'         => array(
         'type'  => 'numberfield',
         'value' => '5',
     ),
     'active'               => array(
         'type'  => 'combo-boolean',
         'value' => true
     ),*/
    'allowAnonym' => array(
        'type'  => 'combo-boolean',
        'value' => false
    ),
    /* 'onlyCreatedby'        => array(
         'type'  => 'combo-boolean',
         'value' => false
     ),*/
    'sortby'      => array(
        'type'  => 'textfield',
        'value' => 'rank'
    ),
    'sortdir'     => array(
        'type'    => 'list',
        'options' => array(
            array('text' => 'ASC', 'value' => 'ASC'),
            array('text' => 'DESC', 'value' => 'DESC')
        ),
        'value'   => 'ASC',
    ),

    /*'allowedFiles'         => array(
        'type'  => 'textfield',
        'value' => 'jpg,jpeg,png,gif,doc,pdf,txt',
    ),
    'source'               => array(
        'type'  => 'numberfield',
        'value' => 0,
    ),
    'tplFile'              => array(
        'type'  => 'textfield',
        'value' => 'tpl.UF.form.file',
    ),
    'tplFiles'             => array(
        'type'  => 'textfield',
        'value' => 'tpl.UF.form.files',
    ),
    'tplImage'             => array(
        'type'  => 'textfield',
        'value' => 'tpl.UF.form.image',
    ),
    'tplUserNoAuth'        => array(
        'type'  => 'textfield',
        'value' => '@INLINE <h5>[[%uf_no_auth]]</h5>',
    ),
    'tplUserNoPermissions' => array(
        'type'  => 'textfield',
        'value' => '@INLINE <h5>[[%uf_no_permissions]]</h5>',
    ),
    'tplWrapper'           => array(
        'type'  => 'textfield',
        'value' => ''
    ),
    'gravatarIcon'         => array(
        'type'  => 'textfield',
        'value' => 'mm',
    ),
    'gravatarSize'         => array(
        'type'  => 'numberfield',
        'value' => '64',
    ),
    'frontendCss'          => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]css/web/default.css',
    ),
    'frontendJs'           => array(
        'type'  => 'textfield',
        'value' => '[[+assetsUrl]]js/web/default.js',
    ),*///

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
        'value' => '[[+assetsUrl]]vendor/jquery/dist/jquery.min.js',
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
        'value' => '{"maxFilesize":1,"maxFiles":2}',
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