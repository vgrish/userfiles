<?php

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

$class = $scriptProperties['class'] = $UserFiles->getOption('class', $scriptProperties, 'modResource', true);
$parent = $scriptProperties['parent'] = $modx->getOption('parent', $scriptProperties);

switch (true) {
    case empty($parent) AND $class == 'modResource':
        $parent = $scriptProperties['parent'] = $modx->resource->id;
        break;
    case empty($parent) AND $class == 'modUser':
        $parent = $scriptProperties['parent'] = $modx->user->id;
        break;
    default:
        break;
}

$list = $scriptProperties['list'] = $UserFiles->getOption('list', $scriptProperties, 'default', true);
$createdby = $scriptProperties['createdby'] = $UserFiles->getOption('createdby', $scriptProperties, $modx->user->id);

$source = $scriptProperties['source'] = $UserFiles->getOption('source', $scriptProperties,
    $UserFiles->getOption('source_default', null, 1, true), true);

$active = $scriptProperties['active'] = (bool)$UserFiles->getOption('active', $scriptProperties, true, true);
$anonym = $scriptProperties['anonym'] = (bool)$UserFiles->getOption('anonym', $scriptProperties, false, true);

$tplForm = $scriptProperties['tplForm'] = $UserFiles->getOption('tplForm', $scriptProperties, 'uf.form', true);
$objectName = $scriptProperties['objectName'] = $UserFiles->getOption('objectName', $scriptProperties, 'UserFilesForm',
    true);
$salt = $scriptProperties['salt'] = $UserFiles->getOption('salt', $scriptProperties, '12345678', true);

$dropzone = trim($modx->getOption('dropzone', $scriptProperties, '{}'));
$dropzone = $scriptProperties['dropzone'] = strpos($dropzone, '{') === 0
    ? $modx->fromJSON($dropzone)
    : array();
$modal = trim($modx->getOption('modal', $scriptProperties, '{}'));
$modal = $scriptProperties['modal'] = strpos($modal, '{') === 0
    ? $modx->fromJSON($modal)
    : array();

$propkey = $scriptProperties['propkey'] = $modx->getOption('propkey', $scriptProperties,
    sha1(serialize($scriptProperties)), true);

$UserFiles->initialize($modx->context->key, $scriptProperties);
$UserFiles->saveProperties($scriptProperties);
$UserFiles->Tools->loadResourceJsCss($scriptProperties);

$row = array(
    'propkey' => $propkey,
);

$output = $UserFiles->getChunk($tplForm, $row);

if (!empty($tplWrapper) AND (!empty($wrapIfEmpty) OR !empty($output))) {
    $output = $UserFiles->getChunk($tplWrapper, array('output' => $output));
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
