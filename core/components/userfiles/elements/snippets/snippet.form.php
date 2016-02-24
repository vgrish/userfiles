<?php

ini_set('display_errors', 1);
ini_set('error_reporting', -1);


/** @var array $scriptProperties */

$corePath = $modx->getOption('userfiles_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
/** @var UserFiles $UserFiles */
$UserFiles = $modx->getService(
    'UserFiles',
    'UserFiles',
    $corePath . 'model/userfiles/',
    array(
        'core_path' => $corePath
    )
);

if (!$UserFiles) {
    return 'Could not load UserFiles class!';
}


$class = $scriptProperties['class'] = $modx->getOption('class', $scriptProperties, 'modResource');
$parent = $scriptProperties['parent'] = $modx->getOption('parent', $scriptProperties);

switch (true) {
    default:
    case empty($parent) AND $class == 'modResource':
        $parent = $scriptProperties['parent'] = $modx->resource->id;
        break;
    case empty($parent) AND $class == 'modUser':
        $parent = $scriptProperties['parent'] = $modx->user->id;
        break;
}

$list = $scriptProperties['list'] = $modx->getOption('list', $scriptProperties, 'default');
$createdby = $scriptProperties['createdby'] = $modx->getOption('createdby', $scriptProperties, $modx->user->id);



$tplForm = $scriptProperties['tplForm'] = $modx->getOption('tplForm', $scriptProperties, 'uf.form');
$objectName = $scriptProperties['objectName'] = $modx->getOption('objectName', $scriptProperties, 'UserFilesForm');


$dropzone = trim($modx->getOption('dropzone', $scriptProperties, '{}'));
$dropzone = $scriptProperties['dropzone'] = strpos($dropzone, '{') === 0
    ? $modx->fromJSON($dropzone)
    : array();

$propkey = $scriptProperties['propkey'] = $modx->getOption('propkey', $scriptProperties,
    sha1(serialize($scriptProperties)), true);


//echo "<pre>";print_r($scriptProperties);

$UserFiles->initialize($modx->context->key, $scriptProperties);
$UserFiles->saveProperties($scriptProperties);
$UserFiles->Tools->loadResourceJsCss($scriptProperties);


$row = array(
    'propkey' => $propkey,
);


//$dropzoneConfig = array(
//    'maxFilesize' => 1,
//    'maxFiles' => 1,
//
//);

//$row['dropzone_config'] = $modx->toJSON($dropzoneConfig);

//'{' . join(',', $properties) . '}';

$output = $UserFiles->getChunk($tplForm, $row);

if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
    $output = $UserFiles->getChunk($tplWrapper, array('output' => $output));
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
