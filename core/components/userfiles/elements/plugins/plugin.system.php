<?php

$corePath = $modx->getOption('userfiles_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/userfiles/');
/** @var UserFiles $UserFiles */
$UserFiles = $modx->getService(
    'userfiles',
    'userfiles',
    $corePath . 'model/userfiles/',
    array(
        'core_path' => $corePath
    )
);

$className = 'UserFiles' . $modx->event->name;
$modx->loadClass('UserFilesPlugin', $UserFiles->getOption('modelPath') . 'userfiles/systems/', true, true);
$modx->loadClass($className, $UserFiles->getOption('modelPath') . 'userfiles/systems/', true, true);
if (class_exists($className)) {
    /** @var $payAndSee $handler */
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
}
return;
